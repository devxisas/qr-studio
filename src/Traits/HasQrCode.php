<?php

declare(strict_types=1);

namespace Devxisas\QrStudio\Traits;

use BadMethodCallException;
use Devxisas\QrStudio\Enums\Format;
use Devxisas\QrStudio\Facades\QrCode;

/**
 * Adds QR code generation to any Eloquent model.
 *
 * ## Basic usage — URL / plain text
 *
 *   class Product extends Model
 *   {
 *       use HasQrCode;
 *
 *       public function qrCodeData(): string
 *       {
 *           return route('products.show', $this);
 *       }
 *   }
 *
 *   echo $product->qrCodeSvg();           // inline SVG
 *   echo $product->qrCodeSvg(300);        // 300 px SVG
 *   echo $product->qrCodeDataUri();       // PNG data URI for <img src="...">
 *
 * ## Structured data types — MeCard, VCard, WiFi, etc.
 *
 *   class Contact extends Model
 *   {
 *       use HasQrCode;
 *
 *       public function qrCodeType(): string { return 'meCard'; }
 *
 *       public function qrCodeData(): array
 *       {
 *           return [
 *               'name'  => $this->last_name . ',' . $this->first_name,
 *               'email' => $this->email,
 *               'phone' => $this->phone ?? '',
 *               'url'   => route('contacts.show', $this),
 *           ];
 *       }
 *   }
 *
 *   // Supported qrCodeType() values (match QrCode magic methods):
 *   //   'meCard', 'vCard', 'wifi', 'email', 'phoneNumber', 'sMS', 'geo', 'bTC', 'calendarEvent'
 */
trait HasQrCode
{
    /**
     * The QR code data type to use when qrCodeData() returns an array.
     *
     * Override this method in your model and return the name of the generator's data type method:
     * 'meCard', 'vCard', 'wifi', 'email', 'phoneNumber', 'sMS', 'geo', 'bTC', 'calendarEvent'
     *
     * Has no effect when qrCodeData() returns a plain string.
     */
    public function qrCodeType(): string
    {
        return 'text';
    }

    /**
     * Returns the data to encode in the QR code.
     *
     * Override this method in your model:
     * - Return a string for plain text or URL QR codes.
     * - Return an array for structured data types (MeCard, VCard, WiFi, etc.)
     *   and set $qrCodeType to the corresponding generator method.
     *
     * @return string|array<string, string>
     *
     * @throws BadMethodCallException if not implemented
     */
    public function qrCodeData(): string|array
    {
        throw new BadMethodCallException(
            'You must implement qrCodeData() in '.static::class.'. '.
            'Return a string for URL/text QR codes, or an array for structured data types '.
            '(meCard, vCard, wifi, etc.) and set $qrCodeType accordingly.'
        );
    }

    /**
     * Generate the QR code and return it as an inline SVG string.
     *
     * @param  int  $size  Width/height in pixels (0 = use package config default)
     */
    public function qrCodeSvg(int $size = 0): string
    {
        return (string) $this->buildGenerator(Format::Svg, $size);
    }

    /**
     * Generate the QR code and return it as a data URI (for use in <img src="...">).
     *
     * @param  int     $size    Width/height in pixels (0 = use package config default)
     * @param  Format  $format  Output format — defaults to PNG for data URIs
     */
    public function qrCodeDataUri(int $size = 0, Format $format = Format::Png): string
    {
        $generator = QrCode::format($format);

        if ($size > 0) {
            $generator->size($size);
        }

        $data = $this->qrCodeData();

        return $generator->toDataUri(is_array($data) ? (string) $this->resolveDataType($data) : $data);
    }

    /**
     * Builds the generator and dispatches to the correct generation path.
     *
     * @return \Illuminate\Support\HtmlString|string|null
     */
    private function buildGenerator(Format $format, int $size): mixed
    {
        $generator = QrCode::format($format);

        if ($size > 0) {
            $generator->size($size);
        }

        $data = $this->qrCodeData();

        if (is_string($data)) {
            return $generator->generate($data);
        }

        // Structured data type — delegate to the generator's magic method
        if ($this->qrCodeType() === 'text') {
            throw new BadMethodCallException(
                'qrCodeData() returned an array in '.static::class.' but $qrCodeType is still "text". '.
                'Set $qrCodeType to a supported data type (e.g. "meCard", "vCard", "wifi").'
            );
        }

        return $generator->{$this->qrCodeType()}($data);
    }

    /**
     * Resolves array data to a string via its data type class (used for toDataUri).
     *
     * @param  array<string, string>  $data
     */
    private function resolveDataType(array $data): string
    {
        if ($this->qrCodeType() === 'text') {
            throw new BadMethodCallException(
                'qrCodeData() returned an array in '.static::class.' but $qrCodeType is still "text".'
            );
        }

        $class = 'Devxisas\\QrStudio\\DataTypes\\'.ucfirst($this->qrCodeType());

        if (! class_exists($class)) {
            throw new BadMethodCallException("DataType [{$this->qrCodeType()}] does not exist.");
        }

        /** @var \Devxisas\QrStudio\DataTypes\DataTypeInterface $instance */
        $instance = new $class;
        $instance->create([$data]);

        return (string) $instance;
    }
}
