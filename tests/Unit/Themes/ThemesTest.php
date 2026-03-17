<?php

declare(strict_types=1);

use Devxisas\QrStudio\Enums\Theme;
use Devxisas\QrStudio\Facades\QrCode;
use Devxisas\QrStudio\Themes\Themes;

it('returns settings array for each built-in theme', function (string $name) {
    $settings = Themes::get($name);

    expect($settings)->toBeArray()->not->toBeEmpty();
})->with(['ocean', 'sunset', 'forest', 'midnight', 'coral']);

it('throws for unknown theme name', function () {
    Themes::get('neon');
})->throws(InvalidArgumentException::class, 'Theme [neon] does not exist');

it('lists all available themes', function () {
    expect(Themes::available())->toContain('ocean', 'sunset', 'forest', 'midnight', 'coral');
});

it('generator theme() method accepts a string', function () {
    $svg = (string) QrCode::theme('ocean')->size(100)->generate('test');

    expect($svg)->toContain('<svg');
});

it('generator theme() method accepts a Theme enum', function () {
    $svg = (string) QrCode::theme(Theme::Midnight)->size(100)->generate('test');

    expect($svg)->toContain('<svg');
});

it('generates valid QR for every built-in theme', function (string $name) {
    $svg = (string) QrCode::theme($name)->size(100)->generate('https://devxi.com');

    expect($svg)->toContain('<svg');
})->with(['ocean', 'sunset', 'forest', 'midnight', 'coral']);

it('per-call options override theme settings', function () {
    $svg = (string) QrCode::theme('ocean')->size(100)->style('square')->generate('override');

    expect($svg)->toContain('<svg');
});
