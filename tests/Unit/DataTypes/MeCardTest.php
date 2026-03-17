<?php

declare(strict_types=1);

use Devxisas\LaravelQrCode\DataTypes\MeCard;

it('generates a basic mecard', function () {
    $mecard = new MeCard;
    $mecard->create([[
        'name' => 'Sorto,Elmer',
        'phone' => '+50312345678',
    ]]);

    $output = (string) $mecard;

    expect($output)->toStartWith('MECARD:');
    expect($output)->toContain('N:Sorto,Elmer;');
    expect($output)->toContain('TEL:+50312345678;');
    expect($output)->toEndWith(';;');
});

it('generates a full mecard with all fields', function () {
    $mecard = new MeCard;
    $mecard->create([[
        'name' => 'Sorto,Elmer',
        'phone' => '+50312345678',
        'email' => 'elmer@devxi.com',
        'url' => 'https://devxisas.com',
        'note' => 'Test note',
    ]]);

    $output = (string) $mecard;

    expect($output)->toContain('EMAIL:elmer@devxi.com;');
    expect($output)->toContain('URL:https\://devxisas.com;');
    expect($output)->toContain('NOTE:Test note;');
});

it('escapes special mecard characters', function () {
    $mecard = new MeCard;
    $mecard->create([[
        'name' => 'Last,First',
        'note' => 'semicolon; and colon:',
    ]]);

    $output = (string) $mecard;

    expect($output)->toContain('NOTE:semicolon\; and colon\:;');
});
