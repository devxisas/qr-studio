<?php

declare(strict_types=1);

use Devxisas\QrStudio\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('local');
});

it('saveToDisk saves a file to the default local disk', function () {
    $path = QrCode::saveToDisk('https://devxi.com', 'test.svg');

    Storage::disk('local')->assertExists($path);
});

it('saveToDisk prepends default path when filename has no directory', function () {
    $path = QrCode::saveToDisk('https://devxi.com', 'qr.svg');

    expect($path)->toBe('qrcodes/qr.svg');
});

it('saveToDisk uses filename as-is when it contains a directory separator', function () {
    $path = QrCode::saveToDisk('https://devxi.com', 'invoices/2025/qr.svg');

    expect($path)->toBe('invoices/2025/qr.svg');
    Storage::disk('local')->assertExists($path);
});

it('saveToDisk uses the specified disk', function () {
    Storage::fake('s3');

    $path = QrCode::saveToDisk('https://devxi.com', 'qr.svg', 's3');

    Storage::disk('s3')->assertExists($path);
    Storage::disk('local')->assertMissing($path);
});

it('saveToDisk resets generator state after saving', function () {
    QrCode::saveToDisk('https://devxi.com', 'qr.svg');

    $path = QrCode::saveToDisk('https://devxi.com', 'qr2.svg');

    Storage::disk('local')->assertExists($path);
});
