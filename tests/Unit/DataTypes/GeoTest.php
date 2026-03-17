<?php

declare(strict_types=1);

use Devxisas\QrStudio\DataTypes\Geo;

it('generates a geo uri', function () {
    $geo = new Geo;
    $geo->create([40.714728, -74.005941]);

    expect((string) $geo)->toBe('geo:40.714728,-74.005941');
});
