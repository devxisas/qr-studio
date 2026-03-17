<?php

declare(strict_types=1);

namespace Devxisas\LaravelQrCode;

use Devxisas\LaravelQrCode\Commands\GenerateQrCodeCommand;
use Devxisas\LaravelQrCode\Enums\Format;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Blade;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelQrCodeServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-qrcode')
            ->hasConfigFile()
            ->hasCommand(GenerateQrCodeCommand::class);
    }

    public function registeringPackage(): void
    {
        $this->app->bind('qrcode', function () {
            $generator = new QrCodeGenerator;

            /** @var array<string, mixed> $config */
            $config = config('laravel-qrcode', []);

            if ($config !== []) {
                $generator->setDefaults($config);
            }

            return $generator;
        });

        $this->app->bind(QrCodeGenerator::class, fn () => $this->app->make('qrcode'));
    }

    public function bootingPackage(): void
    {
        $this->registerBladeDirective();
        $this->registerResponseMacro();
    }

    /**
     * Registers @qrcode blade directive.
     *
     * Usage:
     *
     *   @qrcode('https://devxisas.com')
     *   @qrcode('https://devxisas.com', Format::Svg, 200)
     */
    private function registerBladeDirective(): void
    {
        Blade::directive('qrcode', function (string $expression): string {
            // Split expression into (text, format, size) — format and size are optional.
            // SVG is echoed directly as HTML; PNG is wrapped in <img src="data:..."> since
            // raw binary cannot be embedded inline in HTML.
            return "<?php
                \$__qrArgs = [{$expression}];
                \$__qrGenerator = app('qrcode');
                if (isset(\$__qrArgs[1])) {
                    \$__qrFormat = \$__qrArgs[1] instanceof \Devxisas\LaravelQrCode\Enums\Format
                        ? \$__qrArgs[1]
                        : \Devxisas\LaravelQrCode\Enums\Format::from(strtolower((string) \$__qrArgs[1]));
                    \$__qrGenerator->format(\$__qrFormat);
                } else {
                    \$__cfgFmt = config('laravel-qrcode.format', 'svg');
                    \$__qrFormat = \$__cfgFmt instanceof \Devxisas\LaravelQrCode\Enums\Format
                        ? \$__cfgFmt
                        : \Devxisas\LaravelQrCode\Enums\Format::from(strtolower((string) \$__cfgFmt));
                    unset(\$__cfgFmt);
                }
                if (isset(\$__qrArgs[2])) { \$__qrGenerator->size((int) \$__qrArgs[2]); }
                if (\$__qrFormat === \Devxisas\LaravelQrCode\Enums\Format::Svg) {
                    echo \$__qrGenerator->generate((string) \$__qrArgs[0]);
                } else {
                    \$__qrSize = isset(\$__qrArgs[2]) ? (int) \$__qrArgs[2] : (int) config('laravel-qrcode.size', 200);
                    echo '<img src=\"' . \$__qrGenerator->toDataUri((string) \$__qrArgs[0]) . '\" width=\"' . \$__qrSize . '\" height=\"' . \$__qrSize . '\" alt=\"QR Code\">';
                }
                unset(\$__qrArgs, \$__qrGenerator, \$__qrFormat, \$__qrSize);
            ?>";
        });
    }

    /**
     * Registers response()->qrcode() macro with correct Content-Type headers.
     *
     * Usage:
     *   return response()->qrcode('https://devxisas.com');
     *   return response()->qrcode('https://devxisas.com', Format::Png, 300);
     */
    private function registerResponseMacro(): void
    {
        Response::macro('qrcode', function (
            string $text,
            Format|string $format = Format::Svg,
            int $size = 0,
        ): Response {
            if ($size === 0) {
                $size = (int) config('laravel-qrcode.size', 200);
            }
            /** @var QrCodeGenerator $generator */
            $generator = app('qrcode');

            $resolvedFormat = $format instanceof Format
                ? $format
                : Format::from(strtolower($format));

            $content = (string) $generator
                ->format($resolvedFormat)
                ->size($size)
                ->generate($text);

            /** @var Response $this */
            return $this
                ->setContent($content)
                ->header('Content-Type', $resolvedFormat->mimeType())
                ->header('X-Content-Type-Options', 'nosniff');
        });
    }
}
