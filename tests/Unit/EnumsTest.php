<?php

declare(strict_types=1);

use Devxisas\QrStudio\Enums\ErrorCorrection;
use Devxisas\QrStudio\Enums\EyeStyle;
use Devxisas\QrStudio\Enums\Format;
use Devxisas\QrStudio\Enums\GradientType;
use Devxisas\QrStudio\Enums\Style;
use Devxisas\QrStudio\QrCodeGenerator;
use Illuminate\Support\HtmlString;

// ─── Format enum ──────────────────────────────────────────────────────────────

it('Format enum has correct mime types', function () {
    expect(Format::Svg->mimeType())->toBe('image/svg+xml');
    expect(Format::Png->mimeType())->toBe('image/png');
    expect(Format::Eps->mimeType())->toBe('application/postscript');
});

it('Format enum has correct data URI prefixes', function () {
    expect(Format::Svg->dataUriPrefix())->toBe('data:image/svg+xml;base64,');
    expect(Format::Png->dataUriPrefix())->toBe('data:image/png;base64,');
});

it('format() accepts Format enum', function () {
    $result = (new QrCodeGenerator)->format(Format::Svg)->generate('test');
    expect((string) $result)->toContain('<svg');
});

it('format() accepts string for backward compatibility', function () {
    $result = (new QrCodeGenerator)->format('svg')->generate('test');
    expect((string) $result)->toContain('<svg');
});

it('format() throws for invalid string', function () {
    (new QrCodeGenerator)->format('gif');
})->throws(InvalidArgumentException::class);

// ─── Style enum ───────────────────────────────────────────────────────────────

it('style() accepts Style enum', function () {
    $result = (new QrCodeGenerator)->style(Style::Dot, 0.5)->generate('test');
    expect($result)->toBeInstanceOf(HtmlString::class);
});

it('style() accepts string for backward compatibility', function () {
    $result = (new QrCodeGenerator)->style('dot', 0.5)->generate('test');
    expect($result)->toBeInstanceOf(HtmlString::class);
});

// ─── EyeStyle enum ────────────────────────────────────────────────────────────

it('eye() accepts EyeStyle::Circle enum', function () {
    $result = (new QrCodeGenerator)->eye(EyeStyle::Circle)->generate('test');
    expect($result)->toBeInstanceOf(HtmlString::class);
});

it('eye() accepts EyeStyle::Pointy enum', function () {
    $result = (new QrCodeGenerator)->eye(EyeStyle::Pointy)->generate('test');
    expect($result)->toBeInstanceOf(HtmlString::class);
});

it('eye() accepts string for backward compatibility', function () {
    $result = (new QrCodeGenerator)->eye('circle')->generate('test');
    expect($result)->toBeInstanceOf(HtmlString::class);
});

it('eye() accepts pointy string', function () {
    $result = (new QrCodeGenerator)->eye('pointy')->generate('test');
    expect($result)->toBeInstanceOf(HtmlString::class);
});

// ─── ErrorCorrection enum ─────────────────────────────────────────────────────

it('errorCorrection() accepts ErrorCorrection enum', function () {
    $result = (new QrCodeGenerator)->errorCorrection(ErrorCorrection::High)->generate('test');
    expect($result)->toBeInstanceOf(HtmlString::class);
});

it('errorCorrection() accepts string for backward compatibility', function () {
    $result = (new QrCodeGenerator)->errorCorrection('H')->generate('test');
    expect($result)->toBeInstanceOf(HtmlString::class);
});

it('errorCorrection() throws for invalid string', function () {
    (new QrCodeGenerator)->errorCorrection('Z');
})->throws(InvalidArgumentException::class);

// ─── GradientType enum ────────────────────────────────────────────────────────

it('gradient() accepts GradientType enum', function () {
    $result = (new QrCodeGenerator)
        ->gradient(255, 0, 0, 0, 0, 255, GradientType::Vertical)
        ->generate('test');
    expect($result)->toBeInstanceOf(HtmlString::class);
});

it('gradient() accepts string for backward compatibility', function () {
    $result = (new QrCodeGenerator)
        ->gradient(255, 0, 0, 0, 0, 255, 'vertical')
        ->generate('test');
    expect($result)->toBeInstanceOf(HtmlString::class);
});

it('gradient() throws for invalid string', function () {
    (new QrCodeGenerator)->gradient(0, 0, 0, 255, 255, 255, 'circular');
})->throws(InvalidArgumentException::class);

// ─── toDataUri ────────────────────────────────────────────────────────────────

it('toDataUri returns a valid svg data uri', function () {
    $uri = (new QrCodeGenerator)->toDataUri('https://devxisas.com');

    expect($uri)->toStartWith('data:image/svg+xml;base64,');

    $decoded = base64_decode(substr($uri, strlen('data:image/svg+xml;base64,')));
    expect($decoded)->toContain('<svg');
});

it('toDataUri resets state after generating', function () {
    $generator = new QrCodeGenerator;
    $generator->format(Format::Svg)->toDataUri('test');

    // After toDataUri, state should be reset; next generate should use defaults
    $result = $generator->generate('test');
    expect((string) $result)->toContain('<svg');
});

// ─── size validation ──────────────────────────────────────────────────────────

it('size() throws for zero or negative pixels', function () {
    (new QrCodeGenerator)->size(0);
})->throws(InvalidArgumentException::class);

// ─── margin validation ────────────────────────────────────────────────────────

it('margin() throws for negative margin', function () {
    (new QrCodeGenerator)->margin(-1);
})->throws(InvalidArgumentException::class);

// ─── setDefaults ──────────────────────────────────────────────────────────────

it('setDefaults applies config-driven defaults and reset() respects them', function () {
    $generator = new QrCodeGenerator;
    $generator->setDefaults(['size' => 300, 'format' => 'svg']);

    // Override temporarily
    $generator->size(100)->generate('test');

    // After reset, size should go back to the configured default (300)
    expect($generator->getRendererStyle()->getSize())->toBe(300);
});
