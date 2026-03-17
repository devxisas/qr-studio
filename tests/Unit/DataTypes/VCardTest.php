<?php

declare(strict_types=1);

use Devxisas\QrStudio\DataTypes\VCard;

it('generates a basic vcard', function () {
    $vcard = new VCard;
    $vcard->create([[
        'name' => 'Sorto;Elmer',
        'email' => 'elmer@devxi.com',
    ]]);

    $output = (string) $vcard;

    expect($output)->toContain('BEGIN:VCARD');
    expect($output)->toContain('VERSION:3.0');
    expect($output)->toContain('N:Sorto\;Elmer');
    expect($output)->toContain('EMAIL:elmer@devxi.com');
    expect($output)->toContain('END:VCARD');
});

it('generates a full vcard with all fields', function () {
    $vcard = new VCard;
    $vcard->create([[
        'name' => 'Sorto;Elmer',
        'email' => 'elmer@devxi.com',
        'phone' => '+50312345678',
        'org' => 'Devxisas',
        'title' => 'Developer',
        'url' => 'https://devxisas.com',
        'address' => 'San Salvador',
        'note' => 'Test note',
    ]]);

    $output = (string) $vcard;

    expect($output)->toContain('ORG:Devxisas');
    expect($output)->toContain('TITLE:Developer');
    expect($output)->toContain('TEL;TYPE=WORK,VOICE:+50312345678');
    expect($output)->toContain('URL:https://devxisas.com');
    expect($output)->toContain('NOTE:Test note');
});

it('escapes special characters per RFC 6350', function () {
    $vcard = new VCard;
    $vcard->create([[
        'name' => 'Last;First',
        'note' => 'Has, comma and \\ backslash',
    ]]);

    $output = (string) $vcard;

    expect($output)->toContain('NOTE:Has\, comma and \\\\ backslash');
});

it('generates empty vcard when no data provided', function () {
    $vcard = new VCard;
    $vcard->create([[]]);

    $output = (string) $vcard;

    expect($output)->toContain('BEGIN:VCARD');
    expect($output)->toContain('END:VCARD');
});
