<?php

declare(strict_types=1);

use Devxisas\QrStudio\Enums\ErrorCorrection;
use Devxisas\QrStudio\Enums\Format;
use Devxisas\QrStudio\Enums\Theme;

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

    /*
    |--------------------------------------------------------------------------
    | Default Theme
    |--------------------------------------------------------------------------
    |
    | Apply a built-in visual theme to every QR code by default.
    | Set to null to disable. Override per call with ->theme('ocean').
    |
    | Available: 'ocean', 'sunset', 'forest', 'midnight', 'coral'
    |            or use the Theme enum: Theme::Ocean, Theme::Sunset, etc.
    |
    */
    'theme' => null,

    /*
    |--------------------------------------------------------------------------
    | Storage Disk
    |--------------------------------------------------------------------------
    |
    | The filesystem disk used by saveToDisk() when no disk is specified.
    | Corresponds to a disk defined in config/filesystems.php.
    |
    | Examples: 'local', 's3', 'public'
    |
    */
    'disk' => 'local',

    /*
    |--------------------------------------------------------------------------
    | Storage Path
    |--------------------------------------------------------------------------
    |
    | Default directory used by saveToDisk() when the filename has no
    | directory component (i.e. does not contain '/').
    |
    | Examples: 'qrcodes', 'media/qr', 'invoices/qr'
    |
    */
    'path' => 'qrcodes',

];
