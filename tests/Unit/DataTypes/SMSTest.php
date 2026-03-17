<?php

declare(strict_types=1);

use Devxisas\QrStudio\DataTypes\SMS;

it('generates a sms uri without message', function () {
    $sms = new SMS;
    $sms->create(['+15551234567']);

    expect((string) $sms)->toBe('sms:+15551234567');
});

it('generates a sms uri with message', function () {
    $sms = new SMS;
    $sms->create(['+15551234567', 'Hello there']);

    expect((string) $sms)->toBe('sms:+15551234567&body=Hello there');
});
