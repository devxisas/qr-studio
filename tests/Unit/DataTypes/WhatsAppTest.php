<?php

declare(strict_types=1);

use Devxisas\QrStudio\DataTypes\WhatsApp;

it('generates a whatsapp url with phone only', function () {
    $wa = new WhatsApp;
    $wa->create(['+50312345678']);

    expect((string) $wa)->toBe('https://wa.me/50312345678');
});

it('strips non-numeric characters from phone', function () {
    $wa = new WhatsApp;
    $wa->create(['+1 (800) 555-0100']);

    expect((string) $wa)->toBe('https://wa.me/18005550100');
});

it('appends url-encoded message when provided', function () {
    $wa = new WhatsApp;
    $wa->create(['+50312345678', 'Hola! Vi tu QR.']);

    $output = (string) $wa;

    expect($output)->toStartWith('https://wa.me/50312345678?text=');
    expect($output)->toContain('Hola');
});

it('does not append text param when message is empty', function () {
    $wa = new WhatsApp;
    $wa->create(['+50312345678', '']);

    expect((string) $wa)->toBe('https://wa.me/50312345678');
});
