<?php

declare(strict_types=1);

namespace Devxisas\LaravelQrCode\Facades;

use Devxisas\LaravelQrCode\Enums\ErrorCorrection;
use Devxisas\LaravelQrCode\Enums\EyeStyle;
use Devxisas\LaravelQrCode\Enums\Format;
use Devxisas\LaravelQrCode\Enums\GradientType;
use Devxisas\LaravelQrCode\Enums\Style;
use Devxisas\LaravelQrCode\QrCodeGenerator;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\HtmlString;

/**
 * @method static HtmlString|string|null generate(string $text, ?string $filename = null)
 * @method static string                 toDataUri(string $text)
 * @method static QrCodeGenerator        format(Format|string $format)
 * @method static QrCodeGenerator        size(int $pixels)
 * @method static QrCodeGenerator        color(int $red, int $green, int $blue, ?int $alpha = null)
 * @method static QrCodeGenerator        backgroundColor(int $red, int $green, int $blue, ?int $alpha = null)
 * @method static QrCodeGenerator        eyeColor(int $eyeNumber, int $innerRed, int $innerGreen, int $innerBlue, int $outerRed = 0, int $outerGreen = 0, int $outerBlue = 0)
 * @method static QrCodeGenerator        gradient(int $startRed, int $startGreen, int $startBlue, int $endRed, int $endGreen, int $endBlue, GradientType|string $type)
 * @method static QrCodeGenerator        eye(EyeStyle|string $style)
 * @method static QrCodeGenerator        style(Style|string $style, float $size = 0.5)
 * @method static QrCodeGenerator        encoding(string $encoding)
 * @method static QrCodeGenerator        errorCorrection(ErrorCorrection|string $errorCorrection)
 * @method static QrCodeGenerator        margin(int $margin)
 * @method static QrCodeGenerator        merge(string $filepath, float $percentage = 0.2, bool $absolute = false)
 * @method static QrCodeGenerator        mergeString(string $content, float $percentage = 0.2)
 * @method static QrCodeGenerator        reset()
 * @method static HtmlString|string|null email(string $to, string $subject = '', string $body = '')
 * @method static HtmlString|string|null phoneNumber(string $phoneNumber)
 * @method static HtmlString|string|null sms(string $phoneNumber, string $message = '')
 * @method static HtmlString|string|null geo(float $latitude, float $longitude)
 * @method static HtmlString|string|null btc(string $address, string $amount = '', array $options = [])
 * @method static HtmlString|string|null wifi(array $options)
 * @method static HtmlString|string|null vCard(array $data)
 * @method static HtmlString|string|null meCard(array $data)
 * @method static HtmlString|string|null calendarEvent(array $data)
 *
 * @see QrCodeGenerator
 */
class QrCode extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'qrcode';
    }
}
