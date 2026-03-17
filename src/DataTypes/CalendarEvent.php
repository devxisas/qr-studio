<?php

declare(strict_types=1);

namespace Devxisas\QrStudio\DataTypes;

use DateTimeInterface;
use InvalidArgumentException;

/**
 * Generates an iCal VEVENT QR code.
 *
 * Usage:
 *   QrCode::calendarEvent([
 *       'summary'     => 'Laravel Meetup',
 *       'start'       => '2025-06-15 18:00:00',  // or DateTimeInterface
 *       'end'         => '2025-06-15 20:00:00',
 *       'location'    => 'San Salvador, El Salvador',
 *       'description' => 'Monthly Laravel community meetup',
 *       'url'         => 'https://devxisas.com/events/1',
 *   ])
 */
class CalendarEvent implements DataTypeInterface
{
    private string $summary = '';

    private string $start = '';

    private string $end = '';

    private string $location = '';

    private string $description = '';

    private string $url = '';

    /** @param  array<int, mixed>  $arguments */
    public function create(array $arguments): void
    {
        $data = $arguments[0] ?? [];

        if (! is_array($data)) {
            return;
        }

        $this->summary = $this->escapeText((string) ($data['summary'] ?? ''));
        $this->location = $this->escapeText((string) ($data['location'] ?? ''));
        $this->description = $this->escapeText((string) ($data['description'] ?? ''));
        $this->url = (string) ($data['url'] ?? '');

        $this->start = $this->formatDate($data['start'] ?? null, 'start');
        $this->end = $this->formatDate($data['end'] ?? null, 'end');
    }

    public function __toString(): string
    {
        $lines = ['BEGIN:VEVENT'];

        if ($this->summary !== '') {
            $lines[] = 'SUMMARY:'.$this->summary;
        }

        if ($this->start !== '') {
            $lines[] = 'DTSTART:'.$this->start;
        }

        if ($this->end !== '') {
            $lines[] = 'DTEND:'.$this->end;
        }

        if ($this->location !== '') {
            $lines[] = 'LOCATION:'.$this->location;
        }

        if ($this->description !== '') {
            $lines[] = 'DESCRIPTION:'.$this->description;
        }

        if ($this->url !== '') {
            $lines[] = 'URL:'.$this->url;
        }

        $lines[] = 'END:VEVENT';

        return implode("\r\n", $lines);
    }

    /**
     * Formats a date value to iCal UTC format (YYYYMMDDTHHmmssZ).
     *
     * @param  mixed  $value  DateTimeInterface, timestamp int, or date string
     */
    private function formatDate(mixed $value, string $field): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        if ($value instanceof DateTimeInterface) {
            $ts = $value->getTimestamp();
        } elseif (is_int($value)) {
            $ts = $value;
        } elseif (is_string($value)) {
            $ts = strtotime($value);

            if ($ts === false) {
                throw new InvalidArgumentException(
                    "Could not parse [{$field}] date: \"{$value}\". Use a valid date string or DateTimeInterface."
                );
            }
        } else {
            throw new InvalidArgumentException(
                "Invalid [{$field}] type. Expected string, int timestamp, or DateTimeInterface."
            );
        }

        return gmdate('Ymd\THis\Z', $ts);
    }

    /**
     * Escapes iCal TEXT value special characters per RFC 5545.
     */
    private function escapeText(string $value): string
    {
        $value = str_replace('\\', '\\\\', $value);
        $value = str_replace(';', '\;', $value);
        $value = str_replace(',', '\,', $value);
        $value = str_replace(["\r\n", "\n", "\r"], '\n', $value);

        return $value;
    }
}
