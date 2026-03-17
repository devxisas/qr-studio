<?php

declare(strict_types=1);

namespace Devxisas\QrStudio\Commands;

use Devxisas\QrStudio\Enums\ErrorCorrection;
use Devxisas\QrStudio\Enums\Format;
use Devxisas\QrStudio\QrCodeGenerator;
use Illuminate\Console\Command;
use InvalidArgumentException;

class GenerateQrCodeCommand extends Command
{
    protected $signature = 'qrcode:generate
        {text : The text or URL to encode}
        {--format=svg : Output format: svg, eps, png}
        {--size=200 : Size in pixels (1–4096)}
        {--margin=0 : Margin around the QR code (0 or greater)}
        {--error-correction=M : Error correction level: L (7%), M (15%), Q (25%), H (30%)}
        {--output= : File path to save the QR code (prints to stdout if omitted)}';

    protected $description = 'Generate a QR code from the command line';

    public function handle(QrCodeGenerator $generator): int
    {
        $text = (string) $this->argument('text');

        if (trim($text) === '') {
            $this->error('The text argument cannot be empty.');

            return self::FAILURE;
        }

        // Validate and resolve format
        $formatValue = strtolower((string) $this->option('format'));
        $format = Format::tryFrom($formatValue);

        if ($format === null) {
            $this->error("Invalid format [{$formatValue}]. Allowed values: svg, eps, png.");

            return self::FAILURE;
        }

        // Validate size
        $size = (int) $this->option('size');

        if ($size < 1 || $size > 4096) {
            $this->error("Invalid size [{$size}]. Must be between 1 and 4096 pixels.");

            return self::FAILURE;
        }

        // Validate margin
        $margin = (int) $this->option('margin');

        if ($margin < 0) {
            $this->error("Invalid margin [{$margin}]. Must be 0 or greater.");

            return self::FAILURE;
        }

        // Validate error correction
        $ecValue = strtoupper((string) $this->option('error-correction'));
        $errorCorrection = ErrorCorrection::tryFrom($ecValue);

        if ($errorCorrection === null) {
            $this->error("Invalid error correction [{$ecValue}]. Allowed values: L, M, Q, H.");

            return self::FAILURE;
        }

        // Build generator chain
        $generator
            ->format($format)
            ->size($size)
            ->margin($margin)
            ->errorCorrection($errorCorrection);

        // Resolve output target
        $outputPath = $this->option('output');

        if ($outputPath !== null) {
            $outputPath = (string) $outputPath;
            $this->validateOutputPath($outputPath);
            $generator->generate($text, $outputPath);
            $this->info("QR code saved to [{$outputPath}].");

            return self::SUCCESS;
        }

        // Print raw output to stdout (useful for piping: php artisan qrcode:generate "..." > qr.svg)
        $this->output->write((string) $generator->generate($text));

        return self::SUCCESS;
    }

    /**
     * Validates the output path to prevent directory traversal and ensure writability.
     *
     * @throws InvalidArgumentException
     */
    private function validateOutputPath(string $path): void
    {
        $directory = dirname($path);

        // Resolve the real path of the directory (handles ../ traversal)
        $realDir = realpath($directory);

        if ($realDir === false) {
            throw new InvalidArgumentException("Output directory [{$directory}] does not exist.");
        }

        if (! is_writable($realDir)) {
            throw new InvalidArgumentException("Output directory [{$realDir}] is not writable.");
        }

        // Prevent writing outside the resolved directory
        $resolvedPath = $realDir.DIRECTORY_SEPARATOR.basename($path);
        $realPath = realpath($resolvedPath);

        // File doesn't exist yet — check the resolved path doesn't escape the directory
        if ($realPath === false) {
            $realPath = $resolvedPath;
        }

        if (! str_starts_with($realPath, $realDir)) {
            throw new InvalidArgumentException("Output path [{$path}] is outside the allowed directory.");
        }
    }
}
