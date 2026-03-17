<?php

declare(strict_types=1);

use Devxisas\QrStudio\Enums\ErrorCorrection;
use Devxisas\QrStudio\Enums\Format;

/*
|--------------------------------------------------------------------------
| Publish this config file
|--------------------------------------------------------------------------
|
|   php artisan vendor:publish --tag="qr-studio-config"
|
*/

return [

    /*
    |--------------------------------------------------------------------------
    | Default Format
    |--------------------------------------------------------------------------
    |
    | The default output format for generated QR codes.
    | You can use the Format enum or a plain string.
    |
    | Supported: Format::Svg, Format::Eps, Format::Png
    |            "svg", "eps", "png"
    |
    */
    'format' => Format::Svg,

    /*
    |--------------------------------------------------------------------------
    | Default Size
    |--------------------------------------------------------------------------
    |
    | The default size in pixels for generated QR codes.
    |
    */
    'size' => 100,

    /*
    |--------------------------------------------------------------------------
    | Default Margin
    |--------------------------------------------------------------------------
    |
    | The default margin (quiet zone) around the QR code.
    |
    */
    'margin' => 0,

    /*
    |--------------------------------------------------------------------------
    | Default Error Correction Level
    |--------------------------------------------------------------------------
    |
    | Higher levels allow the QR to be read even when partially damaged,
    | but produce a denser code. Use High (H) when merging logos.
    |
    | Supported: ErrorCorrection::Low    (7%  — L)
    |            ErrorCorrection::Medium (15% — M)  ← default
    |            ErrorCorrection::Quartile (25% — Q)
    |            ErrorCorrection::High   (30% — H)
    |
    */
    'error_correction' => ErrorCorrection::Medium,

    /*
    |--------------------------------------------------------------------------
    | Default Character Encoding
    |--------------------------------------------------------------------------
    |
    | The encoding used when writing data into the QR code.
    | UTF-8 works for virtually all content including URLs, unicode text, etc.
    |
    */
    'encoding' => 'UTF-8',

];
