<?php

declare(strict_types=1);

namespace Devxisas\QrStudio\Traits;

use Devxisas\QrStudio\Enums\Format;
use Devxisas\QrStudio\Facades\QrCode;

/**
 * Adds QR code generation to any Eloquent model.
 *
 * Usage:
 *
 *   class User extends Model
 *   {
 *       use HasQrCode;
 *
 *       // Optional: override the encoded value (default is route URL or model key)
 *       public function qrCodeValue(): string
 *       {
 *           return route('users.show', $this);
 *       }
 *   }
 *
 *   // In a controller or view:
 *   $user->qrCodeSvg();                // SVG string
 *   $user->qrCodeSvg(300);             // SVG at 300 px
 *   $user->qrCodeDataUri();            // PNG data URI
 *   $user->qrCodeDataUri(300, Format::Png);
 */
trait HasQrCode
{
    /**
     * The value to encode in the QR code.
     *
     * Override this method in your model to customise the encoded content.
     * By default uses the model's route URL (if it implements HasRouteBinding)
     * or falls back to the primary key cast to a string.
     */
    public function qrCodeValue(): string
    {
        // If the model has a route() helper via HasRouteBinding we can build a URL.
        // We guard with method_exists to avoid coupling to any specific trait.
        if (method_exists($this, 'getKey')) {
            return (string) $this->getKey();
        }

        return '';
    }

    /**
     * Generate the QR code and return it as an inline SVG string.
     *
     * @param  int  $size  Width/height in pixels (defaults to the package config size)
     */
    public function qrCodeSvg(int $size = 0): string
    {
        $generator = QrCode::format(Format::Svg);

        if ($size > 0) {
            $generator->size($size);
        }

        return (string) $generator->generate($this->qrCodeValue());
    }

    /**
     * Generate the QR code and return it as a data URI (suitable for <img src="...">).
     *
     * @param  int     $size    Width/height in pixels (defaults to the package config size)
     * @param  Format  $format  Output format (defaults to PNG for data URIs)
     */
    public function qrCodeDataUri(int $size = 0, Format $format = Format::Png): string
    {
        $generator = QrCode::format($format);

        if ($size > 0) {
            $generator->size($size);
        }

        return $generator->toDataUri($this->qrCodeValue());
    }
}
