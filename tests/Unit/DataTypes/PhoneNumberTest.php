<?php

declare(strict_types=1);

use Devxisas\LaravelQrCode\DataTypes\PhoneNumber;

it('generates a tel uri', function () {
    $phone = new PhoneNumber;
    $phone->create(['+15551234567']);

    expect((string) $phone)->toBe('tel:+15551234567');
});
