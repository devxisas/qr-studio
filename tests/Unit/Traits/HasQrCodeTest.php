<?php

declare(strict_types=1);

use Devxisas\QrStudio\Traits\HasQrCode;

// A minimal stub model that uses the trait
class StubModel
{
    use HasQrCode;

    public function getKey(): mixed
    {
        return 42;
    }
}

// A stub that overrides the encoded value
class StubModelWithCustomValue
{
    use HasQrCode;

    public function getKey(): mixed
    {
        return 1;
    }

    public function qrCodeValue(): string
    {
        return 'https://devxi.com/users/1';
    }
}

it('qrCodeValue returns model key by default', function () {
    $model = new StubModel;

    expect($model->qrCodeValue())->toBe('42');
});

it('qrCodeValue can be overridden in the model', function () {
    $model = new StubModelWithCustomValue;

    expect($model->qrCodeValue())->toBe('https://devxi.com/users/1');
});

it('qrCodeSvg returns an SVG string', function () {
    $model = new StubModel;

    $svg = $model->qrCodeSvg(100);

    expect($svg)->toContain('<svg');
});

it('qrCodeSvg uses custom value', function () {
    $model = new StubModelWithCustomValue;

    $svg = $model->qrCodeSvg(100);

    expect($svg)->toContain('<svg');
});

it('qrCodeDataUri returns a base64 data URI', function () {
    $model = new StubModel;

    $uri = $model->qrCodeDataUri(100);

    expect($uri)->toStartWith('data:image/');
    expect($uri)->toContain('base64,');
});
