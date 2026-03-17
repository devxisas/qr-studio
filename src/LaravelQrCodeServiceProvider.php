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
            // Split expression into (text, format, size) — format and size are optional
            return "<?php
                \$__qrArgs = [{$expression}];
                \$__qrGenerator = app('qrcode');
                if (isset(\$__qrArgs[1])) { \$__qrGenerator->format(\$__qrArgs[1]); }
                if (isset(\$__qrArgs[2])) { \$__qrGenerator->size((int) \$__qrArgs[2]); }
                echo \$__qrGenerator->generate((string) \$__qrArgs[0]);
                unset(\$__qrArgs, \$__qrGenerator);
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
            int $size = 200,
        ): Response {
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
