<?php

declare(strict_types=1);

namespace Devxisas\QrStudio\DataTypes;

/**
 * Generates a WhatsApp deep-link QR code.
 *
 * Usage:
 *   QrCode::whatsApp('+50312345678')
 *   QrCode::whatsApp('+50312345678', '¡Hola! Vi tu QR.')
 *
 * Scanning the QR opens WhatsApp with the number pre-filled and, if a
 * message is provided, the text pre-typed in the message box.
 *
 * The phone number is normalised to digits only (country code included,
 * no leading +). International format is required: +503 → 503.
 */
class WhatsApp implements DataTypeInterface
{
    private string $phone = '';

    private string $message = '';

    /** @param  array<int, mixed>  $arguments */
    public function create(array $arguments): void
    {
        $raw = (string) ($arguments[0] ?? '');

        // Keep digits only — wa.me requires the number without + or spaces
        $this->phone   = (string) preg_replace('/[^0-9]/', '', $raw);
        $this->message = (string) ($arguments[1] ?? '');
    }

    public function __toString(): string
    {
        $url = 'https://wa.me/'.$this->phone;

        if ($this->message !== '') {
            $url .= '?text='.rawurlencode($this->message);
        }

        return $url;
    }
}
