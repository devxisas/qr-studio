<?php

declare(strict_types=1);

namespace Devxisas\LaravelQrCode\DataTypes;

/**
 * Generates a MeCard QR code (widely supported by iOS and Android).
 *
 * Usage:
 *   QrCode::meCard([
 *       'name'    => 'Hernandez,Elmer',   // Surname,Given
 *       'phone'   => '+50312345678',
 *       'email'   => 'elmer@devxisas.com',
 *       'url'     => 'https://devxisas.com',
 *       'address' => 'San Salvador\,El Salvador',
 *       'note'    => 'Any note',
 *   ])
 */
class MeCard implements DataTypeInterface
{
    private string $name = '';

    private string $phone = '';

    private string $email = '';

    private string $url = '';

    private string $address = '';

    private string $note = '';

    /** @param  array<int, mixed>  $arguments */
    public function create(array $arguments): void
    {
        $data = $arguments[0] ?? [];

        if (! is_array($data)) {
            return;
        }

        $this->name = $this->escape((string) ($data['name'] ?? ''));
        $this->phone = $this->escape((string) ($data['phone'] ?? ''));
        $this->email = $this->escape((string) ($data['email'] ?? ''));
        $this->url = $this->escape((string) ($data['url'] ?? ''));
        $this->address = $this->escape((string) ($data['address'] ?? ''));
        $this->note = $this->escape((string) ($data['note'] ?? ''));
    }

    public function __toString(): string
    {
        $parts = ['MECARD:'];

        if ($this->name !== '') {
            $parts[] = 'N:'.$this->name.';';
        }

        if ($this->phone !== '') {
            $parts[] = 'TEL:'.$this->phone.';';
        }

        if ($this->email !== '') {
            $parts[] = 'EMAIL:'.$this->email.';';
        }

        if ($this->url !== '') {
            $parts[] = 'URL:'.$this->url.';';
        }

        if ($this->address !== '') {
            $parts[] = 'ADR:'.$this->address.';';
        }

        if ($this->note !== '') {
            $parts[] = 'NOTE:'.$this->note.';';
        }

        // MeCard must end with double semicolon
        return implode('', $parts).';';
    }

    /**
     * Escapes special MeCard characters: backslash, semicolon, colon, comma.
     */
    private function escape(string $value): string
    {
        $value = str_replace('\\', '\\\\', $value);
        $value = str_replace(';', '\;', $value);
        $value = str_replace(':', '\:', $value);
        $value = str_replace('"', '\"', $value);

        return $value;
    }
}
