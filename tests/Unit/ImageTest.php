<?php

declare(strict_types=1);

use Devxisas\LaravelQrCode\Image;
use Devxisas\LaravelQrCode\QrCodeGenerator;

it('creates an image from a png string', function () {
    $png = (new QrCodeGenerator)->format('png')->generate('test');
    $image = new Image((string) $png);

    expect($image->getWidth())->toBeInt()->toBeGreaterThan(0);
    expect($image->getHeight())->toBeInt()->toBeGreaterThan(0);
});

it('returns a GdImage resource', function () {
    $png = (new QrCodeGenerator)->format('png')->generate('test');
    $image = new Image((string) $png);

    expect($image->getImageResource())->toBeInstanceOf(GdImage::class);
});

it('allows replacing the image resource', function () {
    $png = (new QrCodeGenerator)->format('png')->generate('test');
    $image = new Image((string) $png);

    $newResource = imagecreatetruecolor(50, 50);
    $image->setImageResource($newResource);

    expect($image->getWidth())->toBe(50);
    expect($image->getHeight())->toBe(50);
});

it('throws an exception for invalid image data', function () {
    new Image('not-valid-image-data');
})->throws(InvalidArgumentException::class);
