<?php

declare(strict_types=1);

use Devxisas\LaravelQrCode\DataTypes\WiFi;

it('generates a wifi string', function () {
    $wifi = new WiFi;
    $wifi->create([['encryption' => 'WPA', 'ssid' => 'MyNetwork', 'password' => 'secret']]);

    expect((string) $wifi)->toBe('WIFI:T:WPA;S:MyNetwork;P:secret;');
});

it('generates a wifi string with hidden flag', function () {
    $wifi = new WiFi;
    $wifi->create([['encryption' => 'WPA', 'ssid' => 'MyNetwork', 'password' => 'secret', 'hidden' => true]]);

    expect((string) $wifi)->toBe('WIFI:T:WPA;S:MyNetwork;P:secret;H:true;');
});
