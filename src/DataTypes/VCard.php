<?php

declare(strict_types=1);

namespace Devxisas\LaravelQrCode\DataTypes;

/**
 * Generates a vCard 3.0 QR code.
 *
 * Usage:
 *   QrCode::vCard([
 *       'name'       => 'Hernandez;Elmer',   // Last;First
 *       'email'      => 'elmer@devxisas.com',
 *       'phone'      => '+50312345678',
 *       'org'        => 'Devxisas',
 *       'title'      => 'Developer',
 *       'url'        => 'https://devxisas.com',
 *       'address'    => 'San Salvador, El Salvador',
 *       'note'       => 'Any note here',
 *   ])
 */
class VCard implements DataTypeInterface
{
    private string $name = '';

    private string $email = '';

    private string $phone = '';

    private string $org = '';

    private string $title = '';

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
        $this->email = $this->escape((string) ($data['email'] ?? ''));
        $this->phone = $this->escape((string) ($data['phone'] ?? ''));
        $this->org = $this->escape((string) ($data['org'] ?? ''));
        $this->title = $this->escape((string) ($data['title'] ?? ''));
        $this->url = $this->escape((string) ($data['url'] ?? ''));
        $this->address = $this->escape((string) ($data['address'] ?? ''));
        $this->note = $this->escape((string) ($data['note'] ?? ''));
    }

    public function __toString(): string
    {
        $lines = ['BEGIN:VCARD', 'VERSION:3.0'];

        if ($this->name !== '') {
            $lines[] = 'N:'.$this->name;
            // FN (formatted name): use the part after the semicolon if present
            $parts = explode(';', $this->name, 2);
            $formatted = isset($parts[1]) ? trim($parts[1]).' '.trim($parts[0]) : trim($parts[0]);
            $lines[] = 'FN:'.trim($formatted);
        }

        if ($this->org !== '') {
            $lines[] = 'ORG:'.$this->org;
        }

        if ($this->title !== '') {
            $lines[] = 'TITLE:'.$this->title;
        }

        if ($this->phone !== '') {
            $lines[] = 'TEL;TYPE=WORK,VOICE:'.$this->phone;
        }

        if ($this->email !== '') {
            $lines[] = 'EMAIL:'.$this->email;
        }

        if ($this->url !== '') {
            $lines[] = 'URL:'.$this->url;
        }

        if ($this->address !== '') {
            $lines[] = 'ADR;TYPE=WORK:;;'.$this->address.';;;;';
        }

        if ($this->note !== '') {
            $lines[] = 'NOTE:'.$this->note;
        }

        $lines[] = 'END:VCARD';

        return implode("\r\n", $lines);
    }

    /**
     * Escapes special characters per RFC 6350.
     * Backslash must be escaped first to avoid double-escaping.
     */
    private function escape(string $value): string
    {
        $value = str_replace('\\', '\\\\', $value);
        $value = str_replace(';', '\;', $value);
        $value = str_replace(',', '\,', $value);
        $value = str_replace(["\r\n", "\n", "\r"], '\n', $value);

        return $value;
    }
}
