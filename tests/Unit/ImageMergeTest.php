<?php

declare(strict_types=1);

use Devxisas\QrStudio\Image;
use Devxisas\QrStudio\ImageMerge;
use Devxisas\QrStudio\QrCodeGenerator;

function makePng(int $width, int $height): string
{
    $img = imagecreatetruecolor($width, $height);
    ob_start();
    imagepng($img);

    return (string) ob_get_clean();
}

it('merges two images and returns a png string', function () {
    $source = new Image(makePng(200, 200));
    $overlay = new Image(makePng(100, 100));

    $result = (new ImageMerge($source, $overlay))->merge(0.2);

    expect($result)->toBeString()->not->toBeEmpty();

    $check = imagecreatefromstring($result);
    expect($check)->toBeInstanceOf(GdImage::class);
});

it('centers the overlay on the source image', function () {
    $source = new Image(makePng(200, 200));
    $overlay = new Image(makePng(100, 100));

    $result = (new ImageMerge($source, $overlay))->merge(0.2);

    $img = imagecreatefromstring($result);
    expect(imagesx($img))->toBe(200);
    expect(imagesy($img))->toBe(200);
});

it('throws an exception when percentage is greater than 1', function () {
    $source = new Image(makePng(200, 200));
    $overlay = new Image(makePng(100, 100));

    (new ImageMerge($source, $overlay))->merge(1.1);
})->throws(InvalidArgumentException::class);

it('merges a logo onto a generated png qr code', function () {
    $qrPng = (string) (new QrCodeGenerator)->format('png')->size(200)->generate('https://devxisas.com');
    $logo = makePng(50, 50);

    $source = new Image($qrPng);
    $overlay = new Image($logo);
    $result = (new ImageMerge($source, $overlay))->merge(0.2);

    expect($result)->toBeString()->not->toBeEmpty();
    expect(imagecreatefromstring($result))->toBeInstanceOf(GdImage::class);
});
