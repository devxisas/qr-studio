<?php

declare(strict_types=1);

use Devxisas\LaravelQrCode\DataTypes\BTC;

it('generates a bitcoin uri with address only', function () {
    $btc = new BTC;
    $btc->create(['1A1zP1eP5QGefi2DMPTfTL5SLmv7Divf']);

    expect((string) $btc)->toBe('bitcoin:1A1zP1eP5QGefi2DMPTfTL5SLmv7Divf');
});

it('generates a bitcoin uri with amount', function () {
    $btc = new BTC;
    $btc->create(['1A1zP1eP5QGefi2DMPTfTL5SLmv7Divf', '0.5']);

    expect((string) $btc)->toContain('bitcoin:1A1zP1eP5QGefi2DMPTfTL5SLmv7Divf');
    expect((string) $btc)->toContain('amount=0.5');
});

it('generates a bitcoin uri with options', function () {
    $btc = new BTC;
    $btc->create(['1A1zP1eP5QGefi2DMPTfTL5SLmv7Divf', '0.5', ['label' => 'Donation', 'message' => 'Thanks']]);

    expect((string) $btc)->toContain('label=Donation');
    expect((string) $btc)->toContain('message=Thanks');
});
