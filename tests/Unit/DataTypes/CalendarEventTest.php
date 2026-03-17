<?php

declare(strict_types=1);

use Devxisas\QrStudio\DataTypes\CalendarEvent;

it('generates a basic calendar event', function () {
    $event = new CalendarEvent;
    $event->create([[
        'summary' => 'Laravel Meetup',
        'start' => '2025-06-15 18:00:00',
        'end' => '2025-06-15 20:00:00',
    ]]);

    $output = (string) $event;

    expect($output)->toContain('BEGIN:VEVENT');
    expect($output)->toContain('SUMMARY:Laravel Meetup');
    expect($output)->toContain('DTSTART:20250615T180000Z');
    expect($output)->toContain('DTEND:20250615T200000Z');
    expect($output)->toContain('END:VEVENT');
});

it('generates a full calendar event', function () {
    $event = new CalendarEvent;
    $event->create([[
        'summary' => 'Dev Conference',
        'start' => '2025-09-01 09:00:00',
        'end' => '2025-09-01 18:00:00',
        'location' => 'San Salvador, El Salvador',
        'description' => 'Annual developer conference',
        'url' => 'https://devxisas.com/conf',
    ]]);

    $output = (string) $event;

    expect($output)->toContain('LOCATION:San Salvador\, El Salvador');
    expect($output)->toContain('DESCRIPTION:Annual developer conference');
    expect($output)->toContain('URL:https://devxisas.com/conf');
});

it('accepts a DateTimeInterface as start/end', function () {
    $event = new CalendarEvent;
    $event->create([[
        'summary' => 'Test',
        'start' => new DateTime('2025-01-01 12:00:00', new DateTimeZone('UTC')),
        'end' => new DateTime('2025-01-01 13:00:00', new DateTimeZone('UTC')),
    ]]);

    $output = (string) $event;

    expect($output)->toContain('DTSTART:20250101T120000Z');
    expect($output)->toContain('DTEND:20250101T130000Z');
});

it('accepts a unix timestamp as start/end', function () {
    $ts = mktime(12, 0, 0, 1, 1, 2025);
    $event = new CalendarEvent;
    $event->create([['summary' => 'Test', 'start' => $ts]]);

    expect((string) $event)->toContain('DTSTART:');
});

it('throws for an invalid date string', function () {
    $event = new CalendarEvent;
    $event->create([['summary' => 'Test', 'start' => 'not-a-date']]);
})->throws(InvalidArgumentException::class);

it('escapes special characters in summary', function () {
    $event = new CalendarEvent;
    $event->create([[
        'summary' => 'Event, with; special\chars',
        'start' => '2025-01-01 12:00:00',
    ]]);

    $output = (string) $event;

    expect($output)->toContain('SUMMARY:Event\, with\; special\\\\chars');
});
