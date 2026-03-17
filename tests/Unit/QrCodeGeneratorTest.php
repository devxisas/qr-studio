<?php

declare(strict_types=1);

use BaconQrCode\Renderer\Eye\SimpleCircleEye;
use BaconQrCode\Renderer\Eye\SquareEye;
use BaconQrCode\Renderer\Image\EpsImageBackEnd;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\Module\DotsModule;
use BaconQrCode\Renderer\Module\RoundnessModule;
use BaconQrCode\Renderer\Module\SquareModule;
use BaconQrCode\Renderer\RendererStyle\Gradient;
use Devxisas\LaravelQrCode\QrCodeGenerator;
use Illuminate\Support\HtmlString;

it('generates an svg qr code by default', function () {
    $result = (new QrCodeGenerator)->generate('https://devxisas.com');

    expect($result)->toBeInstanceOf(HtmlString::class);
    expect((string) $result)->toContain('<svg');
});

it('generates a qr code with custom size', function () {
    $result = (new QrCodeGenerator)->size(200)->generate('hello');

    expect($result)->toBeInstanceOf(HtmlString::class);
});

it('generates a qr code in eps format', function () {
    $result = (new QrCodeGenerator)->format('eps')->generate('hello');

    expect((string) $result)->toContain('%!PS-Adobe');
});

it('writes a qr code to a file and returns null', function () {
    $path = tempnam(sys_get_temp_dir(), 'qr_').'svg';

    $result = (new QrCodeGenerator)->generate('test', $path);

    expect($result)->toBeNull();
    expect(file_exists($path))->toBeTrue();
    expect(file_get_contents($path))->toContain('<svg');

    unlink($path);
});

it('throws an exception for an invalid format', function () {
    (new QrCodeGenerator)->format('gif');
})->throws(InvalidArgumentException::class);

it('throws an exception for an invalid eye number', function () {
    (new QrCodeGenerator)->eyeColor(3, 0, 0, 0);
})->throws(InvalidArgumentException::class);

it('throws an exception for an invalid style', function () {
    (new QrCodeGenerator)->style('triangle');
})->throws(InvalidArgumentException::class);

it('throws an exception for an invalid eye style', function () {
    (new QrCodeGenerator)->eye('diamond');
})->throws(InvalidArgumentException::class);

it('generates a qr code with dot style', function () {
    $result = (new QrCodeGenerator)->style('dot', 0.5)->generate('hello');

    expect($result)->toBeInstanceOf(HtmlString::class);
});

it('generates a qr code with round style', function () {
    $result = (new QrCodeGenerator)->style('round', 0.5)->generate('hello');

    expect($result)->toBeInstanceOf(HtmlString::class);
});

it('generates a qr code with circle eye style', function () {
    $result = (new QrCodeGenerator)->eye('circle')->generate('hello');

    expect($result)->toBeInstanceOf(HtmlString::class);
});

it('generates a qr code with custom foreground color', function () {
    $result = (new QrCodeGenerator)->color(255, 0, 0)->generate('hello');

    expect($result)->toBeInstanceOf(HtmlString::class);
});

it('generates a qr code with custom background color', function () {
    $result = (new QrCodeGenerator)->backgroundColor(255, 255, 0)->generate('hello');

    expect($result)->toBeInstanceOf(HtmlString::class);
});

it('generates a qr code with custom eye colors', function () {
    $result = (new QrCodeGenerator)
        ->eyeColor(0, 255, 0, 0, 0, 0, 255)
        ->generate('hello');

    expect($result)->toBeInstanceOf(HtmlString::class);
});

it('generates a qr code with custom margin', function () {
    $result = (new QrCodeGenerator)->margin(4)->generate('hello');

    expect($result)->toBeInstanceOf(HtmlString::class);
});

it('generates a qr code with error correction', function () {
    $result = (new QrCodeGenerator)->errorCorrection('H')->generate('hello');

    expect($result)->toBeInstanceOf(HtmlString::class);
});

it('generates a qr code with custom encoding', function () {
    $result = (new QrCodeGenerator)->encoding('UTF-8')->generate('hello');

    expect($result)->toBeInstanceOf(HtmlString::class);
});

it('generates a qr code with a gradient', function () {
    $result = (new QrCodeGenerator)
        ->gradient(255, 0, 0, 0, 0, 255, 'vertical')
        ->generate('hello');

    expect($result)->toBeInstanceOf(HtmlString::class);
});

it('throws an exception for a negative eye number', function () {
    (new QrCodeGenerator)->eyeColor(-1, 0, 0, 0);
})->throws(InvalidArgumentException::class);

it('throws an exception for a style size of exactly 1', function () {
    (new QrCodeGenerator)->style('round', 1.0);
})->throws(InvalidArgumentException::class);

it('throws an exception for a style size above 1', function () {
    (new QrCodeGenerator)->style('round', 1.1);
})->throws(InvalidArgumentException::class);

it('throws an exception for a style size below 0', function () {
    (new QrCodeGenerator)->style('round', -0.1);
})->throws(InvalidArgumentException::class);

it('throws a BadMethodCallException for unknown data types', function () {
    (new QrCodeGenerator)->notReal('foo');
})->throws(BadMethodCallException::class);

it('returns the correct formatter instances', function () {
    expect((new QrCodeGenerator)->format('svg')->getFormatter())
        ->toBeInstanceOf(SvgImageBackEnd::class);

    expect((new QrCodeGenerator)->format('eps')->getFormatter())
        ->toBeInstanceOf(EpsImageBackEnd::class);
});

it('returns the correct module instances', function () {
    expect((new QrCodeGenerator)->style('square')->getModule())
        ->toBeInstanceOf(SquareModule::class);

    expect((new QrCodeGenerator)->style('dot', 0.5)->getModule())
        ->toBeInstanceOf(DotsModule::class);

    expect((new QrCodeGenerator)->style('round', 0.5)->getModule())
        ->toBeInstanceOf(RoundnessModule::class);
});

it('returns the correct eye instances', function () {
    expect((new QrCodeGenerator)->eye('circle')->getEye())
        ->toBeInstanceOf(SimpleCircleEye::class);

    expect((new QrCodeGenerator)->eye('square')->getEye())
        ->toBeInstanceOf(SquareEye::class);
});

it('sets gradient correctly', function () {
    $generator = (new QrCodeGenerator)->gradient(0, 0, 0, 255, 255, 255, 'vertical');

    expect($generator->getFill()->getForegroundGradient())
        ->toBeInstanceOf(Gradient::class);
});

it('passes size to the renderer style', function () {
    $generator = (new QrCodeGenerator)->size(250);

    expect($generator->getRendererStyle()->getSize())->toBe(250);
});

it('all chainable methods return static', function () {
    $g = new QrCodeGenerator;

    expect($g->size(100))->toBeInstanceOf(QrCodeGenerator::class);
    expect($g->format('svg'))->toBeInstanceOf(QrCodeGenerator::class);
    expect($g->color(0, 0, 0))->toBeInstanceOf(QrCodeGenerator::class);
    expect($g->backgroundColor(255, 255, 255))->toBeInstanceOf(QrCodeGenerator::class);
    expect($g->eyeColor(0, 0, 0, 0))->toBeInstanceOf(QrCodeGenerator::class);
    expect($g->gradient(0, 0, 0, 255, 255, 255, 'vertical'))->toBeInstanceOf(QrCodeGenerator::class);
    expect($g->eye('circle'))->toBeInstanceOf(QrCodeGenerator::class);
    expect($g->style('dot', 0.5))->toBeInstanceOf(QrCodeGenerator::class);
    expect($g->encoding('UTF-8'))->toBeInstanceOf(QrCodeGenerator::class);
    expect($g->errorCorrection('H'))->toBeInstanceOf(QrCodeGenerator::class);
    expect($g->margin(2))->toBeInstanceOf(QrCodeGenerator::class);
    expect($g->mergeString('fake', 0.2))->toBeInstanceOf(QrCodeGenerator::class);
    expect($g->reset())->toBeInstanceOf(QrCodeGenerator::class);
});

it('resets state after generate so format does not leak between calls', function () {
    $generator = new QrCodeGenerator;

    $generator->format('png')->generate('test');

    $result = $generator->generate('test');

    expect((string) $result)->toContain('<svg');
});

it('returns an HtmlString when illuminate/support is available', function () {
    expect((new QrCodeGenerator)->generate('hello'))
        ->toBeInstanceOf(HtmlString::class);
});
