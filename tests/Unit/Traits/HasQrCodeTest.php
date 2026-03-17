<?php

declare(strict_types=1);

use Devxisas\QrStudio\Traits\HasQrCode;

// ── Stubs ─────────────────────────────────────────────────────────────────────

// URL / plain-text model
class StubUrlModel
{
    use HasQrCode;

    public function qrCodeData(): string
    {
        return 'https://devxi.com/products/1';
    }
}

// Structured data — MeCard
class StubContactModel
{
    use HasQrCode;

    public function qrCodeType(): string
    {
        return 'meCard';
    }

    public function qrCodeData(): array
    {
        return [
            'name' => 'Sorto,Elmer',
            'email' => 'elmer@devxi.com',
        ];
    }
}

// Model that forgets to implement qrCodeData()
class StubUnimplementedModel
{
    use HasQrCode;
}

// Model that returns an array but leaves $qrCodeType = 'text'
class StubBadTypeModel
{
    use HasQrCode;

    public function qrCodeData(): array
    {
        return ['name' => 'Test'];
    }
}

// ── Tests ─────────────────────────────────────────────────────────────────────

it('throws when qrCodeData() is not implemented', function () {
    $model = new StubUnimplementedModel;
    $model->qrCodeSvg();
})->throws(BadMethodCallException::class, 'You must implement qrCodeData()');

it('qrCodeSvg returns an SVG string for a URL model', function () {
    $model = new StubUrlModel;

    expect($model->qrCodeSvg(100))->toContain('<svg');
});

it('qrCodeSvg works without explicit size (uses config default)', function () {
    $model = new StubUrlModel;

    expect($model->qrCodeSvg())->toContain('<svg');
});

it('qrCodeSvg works with a structured MeCard type', function () {
    $model = new StubContactModel;

    expect($model->qrCodeSvg(100))->toContain('<svg');
});

it('qrCodeDataUri returns a base64 data URI for a URL model', function () {
    $model = new StubUrlModel;

    $uri = $model->qrCodeDataUri(100);

    expect($uri)->toStartWith('data:image/');
    expect($uri)->toContain('base64,');
});

it('qrCodeDataUri works with a structured MeCard type', function () {
    $model = new StubContactModel;

    $uri = $model->qrCodeDataUri(100);

    expect($uri)->toStartWith('data:image/');
    expect($uri)->toContain('base64,');
});

it('throws when data is an array but qrCodeType is still "text"', function () {
    $model = new StubBadTypeModel;
    $model->qrCodeSvg();
})->throws(BadMethodCallException::class, '$qrCodeType is still "text"');
