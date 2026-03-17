<?php

declare(strict_types=1);

namespace Devxisas\LaravelQrCode;

use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Encoder\Encoder;
use BaconQrCode\Exception\WriterException;
use BaconQrCode\Renderer\Color\Alpha;
use BaconQrCode\Renderer\Color\ColorInterface;
use BaconQrCode\Renderer\Color\Rgb;
use BaconQrCode\Renderer\Eye\EyeInterface;
use BaconQrCode\Renderer\Eye\ModuleEye;
use BaconQrCode\Renderer\Eye\SimpleCircleEye;
use BaconQrCode\Renderer\Eye\SquareEye;
use BaconQrCode\Renderer\Image\EpsImageBackEnd;
use BaconQrCode\Renderer\Image\ImageBackEndInterface;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Module\DotsModule;
use BaconQrCode\Renderer\Module\ModuleInterface;
use BaconQrCode\Renderer\Module\RoundnessModule;
use BaconQrCode\Renderer\Module\SquareModule;
use BaconQrCode\Renderer\RendererStyle\EyeFill;
use BaconQrCode\Renderer\RendererStyle\Fill;
use BaconQrCode\Renderer\RendererStyle\Gradient;
use BaconQrCode\Renderer\RendererStyle\GradientType as BaconGradientType;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use BadMethodCallException;
use Devxisas\LaravelQrCode\DataTypes\DataTypeInterface;
use Devxisas\LaravelQrCode\Enums\ErrorCorrection;
use Devxisas\LaravelQrCode\Enums\EyeStyle;
use Devxisas\LaravelQrCode\Enums\Format;
use Devxisas\LaravelQrCode\Enums\GradientType;
use Devxisas\LaravelQrCode\Enums\Style;
use Illuminate\Support\HtmlString;
use InvalidArgumentException;

class QrCodeGenerator
{
    // Defaults — optionally overridden via config in the ServiceProvider
    protected Format $defaultFormat = Format::Svg;

    protected int $defaultPixels = 100;

    protected int $defaultMargin = 0;

    protected ErrorCorrection $defaultErrorCorrection = ErrorCorrection::Medium;

    // Current state
    protected Format $format;

    protected int $pixels;

    protected int $margin;

    protected ?ErrorCorrectionLevel $errorCorrection = null;

    protected string $encoding = Encoder::DEFAULT_BYTE_MODE_ECODING;

    protected Style $style = Style::Square;

    protected ?float $styleSize = null;

    protected ?EyeStyle $eyeStyle = null;

    protected ?ColorInterface $color = null;

    protected ?ColorInterface $backgroundColor = null;

    /** @var array<int, EyeFill> */
    protected array $eyeColors = [];

    protected ?Gradient $gradient = null;

    protected ?string $imageMerge = null;

    protected float $imagePercentage = 0.2;

    public function __construct()
    {
        $this->reset();
    }

    /**
     * Sets config-driven defaults. Called by the ServiceProvider after instantiation.
     *
     * @param  array<string, mixed>  $defaults
     */
    public function setDefaults(array $defaults): static
    {
        if (isset($defaults['format'])) {
            $this->defaultFormat = $defaults['format'] instanceof Format
                ? $defaults['format']
                : Format::from($defaults['format']);
        }

        if (isset($defaults['size']) && is_int($defaults['size'])) {
            $this->defaultPixels = $defaults['size'];
        }

        if (isset($defaults['margin']) && is_int($defaults['margin'])) {
            $this->defaultMargin = $defaults['margin'];
        }

        if (isset($defaults['error_correction'])) {
            $this->defaultErrorCorrection = $defaults['error_correction'] instanceof ErrorCorrection
                ? $defaults['error_correction']
                : ErrorCorrection::from(strtoupper((string) $defaults['error_correction']));
        }

        return $this->reset();
    }

    /**
     * Resets all state to defaults (config-driven or package defaults).
     * Called automatically after every generate() so the cached Facade instance
     * can be reused cleanly within a request.
     */
    public function reset(): static
    {
        $this->format = $this->defaultFormat;
        $this->pixels = $this->defaultPixels;
        $this->margin = $this->defaultMargin;
        $this->errorCorrection = $this->resolveErrorCorrectionLevel($this->defaultErrorCorrection);
        $this->encoding = Encoder::DEFAULT_BYTE_MODE_ECODING;
        $this->style = Style::Square;
        $this->styleSize = null;
        $this->eyeStyle = null;
        $this->color = null;
        $this->backgroundColor = null;
        $this->eyeColors = [];
        $this->gradient = null;
        $this->imageMerge = null;
        $this->imagePercentage = 0.2;

        return $this;
    }

    /**
     * Creates a new DataType object and generates a QR code via magic method.
     *
     * @param  array<int, mixed>  $arguments
     */
    public function __call(string $method, array $arguments): HtmlString|string|null
    {
        $dataType = $this->createClass($method);
        $dataType->create($arguments);

        return $this->generate((string) $dataType);
    }

    /**
     * Generates the QR code.
     * Returns null when writing to a file, HtmlString otherwise.
     *
     * @throws WriterException
     * @throws InvalidArgumentException
     */
    public function generate(string $text, ?string $filename = null): HtmlString|string|null
    {
        $qrCode = $this->getWriter($this->getRenderer())->writeString($text, $this->encoding, $this->errorCorrection);

        if ($this->imageMerge !== null && $this->format === Format::Png) {
            $merger = new ImageMerge(new Image($qrCode), new Image($this->imageMerge));
            $qrCode = $merger->merge($this->imagePercentage);
        }

        $result = null;

        if ($filename !== null) {
            file_put_contents($filename, $qrCode);
        } elseif (class_exists(HtmlString::class)) {
            $result = new HtmlString($qrCode);
        } else {
            $result = $qrCode;
        }

        $this->reset();

        return $result;
    }

    /**
     * Generates the QR code and returns it as a base64 data URI.
     * Ideal for embedding in emails or PDFs where external URLs are blocked.
     *
     * Example: <img src="{{ QrCode::size(200)->toDataUri('https://example.com') }}">
     *
     * @throws WriterException
     */
    public function toDataUri(string $text): string
    {
        $format = $this->format; // capture before reset() inside generate()

        $qrCode = $this->getWriter($this->getRenderer())->writeString($text, $this->encoding, $this->errorCorrection);

        if ($this->imageMerge !== null && $format === Format::Png) {
            $merger = new ImageMerge(new Image($qrCode), new Image($this->imageMerge));
            $qrCode = $merger->merge($this->imagePercentage);
        }

        $this->reset();

        return $format->dataUriPrefix().base64_encode($qrCode);
    }

    /**
     * Sets the output format.
     * Accepts a Format enum (recommended) or a string for backward compatibility.
     *
     * @throws InvalidArgumentException
     */
    public function format(Format|string $format): static
    {
        if (is_string($format)) {
            $format = Format::tryFrom(strtolower($format))
                ?? throw new InvalidArgumentException(
                    "\$format must be svg, eps, or png. [{$format}] is not valid."
                );
        }

        $this->format = $format;

        return $this;
    }

    /**
     * Sets the size of the QR code in pixels.
     *
     * @throws InvalidArgumentException
     */
    public function size(int $pixels): static
    {
        if ($pixels < 1) {
            throw new InvalidArgumentException("\$pixels must be greater than 0. [{$pixels}] is not valid.");
        }

        $this->pixels = $pixels;

        return $this;
    }

    /**
     * Sets the foreground color of the QR code.
     */
    public function color(int $red, int $green, int $blue, ?int $alpha = null): static
    {
        $this->color = $this->createColor($red, $green, $blue, $alpha);

        return $this;
    }

    /**
     * Sets the background color of the QR code.
     */
    public function backgroundColor(int $red, int $green, int $blue, ?int $alpha = null): static
    {
        $this->backgroundColor = $this->createColor($red, $green, $blue, $alpha);

        return $this;
    }

    /**
     * Sets the eye color for the given eye index (0, 1, or 2).
     *
     * @throws InvalidArgumentException
     */
    public function eyeColor(
        int $eyeNumber,
        int $innerRed, int $innerGreen, int $innerBlue,
        int $outerRed = 0, int $outerGreen = 0, int $outerBlue = 0,
    ): static {
        if ($eyeNumber < 0 || $eyeNumber > 2) {
            throw new InvalidArgumentException("\$eyeNumber must be 0, 1, or 2. [{$eyeNumber}] is not valid.");
        }

        $this->eyeColors[$eyeNumber] = new EyeFill(
            $this->createColor($innerRed, $innerGreen, $innerBlue),
            $this->createColor($outerRed, $outerGreen, $outerBlue)
        );

        return $this;
    }

    /**
     * Applies a gradient to the QR code.
     * Accepts a GradientType enum (recommended) or a string for backward compatibility.
     *
     * @throws InvalidArgumentException
     */
    public function gradient(
        int $startRed, int $startGreen, int $startBlue,
        int $endRed, int $endGreen, int $endBlue,
        GradientType|string $type,
    ): static {
        if (is_string($type)) {
            $type = GradientType::tryFrom(strtolower($type))
                ?? throw new InvalidArgumentException(
                    "Invalid gradient type [{$type}]. Valid values: "
                    .implode(', ', array_column(GradientType::cases(), 'value')).'.'
                );
        }

        $baconType = $type->toBaconType();

        $this->gradient = new Gradient(
            $this->createColor($startRed, $startGreen, $startBlue),
            $this->createColor($endRed, $endGreen, $endBlue),
            BaconGradientType::$baconType()
        );

        return $this;
    }

    /**
     * Sets the eye style.
     * Accepts an EyeStyle enum (recommended) or a string for backward compatibility.
     *
     * @throws InvalidArgumentException
     */
    public function eye(EyeStyle|string $style): static
    {
        if (is_string($style)) {
            $style = EyeStyle::tryFrom(strtolower($style))
                ?? throw new InvalidArgumentException(
                    "\$style must be square or circle. [{$style}] is not a valid eye style."
                );
        }

        $this->eyeStyle = $style;

        return $this;
    }

    /**
     * Sets the module style.
     * Accepts a Style enum (recommended) or a string for backward compatibility.
     *
     * @throws InvalidArgumentException
     */
    public function style(Style|string $style, float $size = 0.5): static
    {
        if (is_string($style)) {
            $style = Style::tryFrom(strtolower($style))
                ?? throw new InvalidArgumentException(
                    "\$style must be square, dot, or round. [{$style}] is not valid."
                );
        }

        if ($size < 0 || $size >= 1) {
            throw new InvalidArgumentException("\$size must be between 0 and 1. [{$size}] is not valid.");
        }

        $this->style = $style;
        $this->styleSize = $size;

        return $this;
    }

    /**
     * Sets the character encoding.
     */
    public function encoding(string $encoding): static
    {
        $this->encoding = strtoupper($encoding);

        return $this;
    }

    /**
     * Sets the error correction level.
     * Accepts an ErrorCorrection enum (recommended) or a string for backward compatibility.
     *
     * @throws InvalidArgumentException
     */
    public function errorCorrection(ErrorCorrection|string $errorCorrection): static
    {
        if (is_string($errorCorrection)) {
            $errorCorrection = ErrorCorrection::tryFrom(strtoupper($errorCorrection))
                ?? throw new InvalidArgumentException(
                    "Invalid error correction level [{$errorCorrection}]. Valid values: L, M, Q, H."
                );
        }

        $this->errorCorrection = $this->resolveErrorCorrectionLevel($errorCorrection);

        return $this;
    }

    /**
     * Sets the margin around the QR code.
     *
     * @throws InvalidArgumentException
     */
    public function margin(int $margin): static
    {
        if ($margin < 0) {
            throw new InvalidArgumentException("\$margin must be 0 or greater. [{$margin}] is not valid.");
        }

        $this->margin = $margin;

        return $this;
    }

    /**
     * Merges an image over the QR code from a file path.
     * Only applies when using PNG format.
     */
    public function merge(string $filepath, float $percentage = 0.2, bool $absolute = false): static
    {
        if (function_exists('base_path') && ! $absolute) {
            $filepath = base_path().$filepath;
        }

        $content = file_get_contents($filepath);

        if ($content === false) {
            throw new InvalidArgumentException("Could not read file at [{$filepath}].");
        }

        $this->imageMerge = $content;
        $this->imagePercentage = $percentage;

        return $this;
    }

    /**
     * Merges an image string with the center of the QR code.
     * Only applies when using PNG format.
     */
    public function mergeString(string $content, float $percentage = 0.2): static
    {
        $this->imageMerge = $content;
        $this->imagePercentage = $percentage;

        return $this;
    }

    public function getWriter(ImageRenderer $renderer): Writer
    {
        return new Writer($renderer);
    }

    public function getRenderer(): ImageRenderer
    {
        return new ImageRenderer(
            $this->getRendererStyle(),
            $this->getFormatter()
        );
    }

    public function getRendererStyle(): RendererStyle
    {
        return new RendererStyle(
            $this->pixels,
            $this->margin,
            $this->getModule(),
            $this->getEye(),
            $this->getFill()
        );
    }

    public function getFormatter(): ImageBackEndInterface
    {
        return match ($this->format) {
            Format::Png => new ImagickImageBackEnd,
            Format::Eps => new EpsImageBackEnd,
            default => new SvgImageBackEnd,
        };
    }

    public function getModule(): ModuleInterface
    {
        return match ($this->style) {
            Style::Dot => new DotsModule($this->styleSize ?? 0.5),
            Style::Round => new RoundnessModule($this->styleSize ?? 0.5),
            default => SquareModule::instance(),
        };
    }

    public function getEye(): EyeInterface
    {
        return match ($this->eyeStyle) {
            EyeStyle::Square => SquareEye::instance(),
            EyeStyle::Circle => SimpleCircleEye::instance(),
            default => new ModuleEye($this->getModule()),
        };
    }

    public function getFill(): Fill
    {
        $foregroundColor = $this->color ?? new Rgb(0, 0, 0);
        $backgroundColor = $this->backgroundColor ?? new Rgb(255, 255, 255);
        $eye0 = $this->eyeColors[0] ?? EyeFill::inherit();
        $eye1 = $this->eyeColors[1] ?? EyeFill::inherit();
        $eye2 = $this->eyeColors[2] ?? EyeFill::inherit();

        if ($this->gradient !== null) {
            return Fill::withForegroundGradient($backgroundColor, $this->gradient, $eye0, $eye1, $eye2);
        }

        return Fill::withForegroundColor($backgroundColor, $foregroundColor, $eye0, $eye1, $eye2);
    }

    public function createColor(int $red, int $green, int $blue, ?int $alpha = null): ColorInterface
    {
        if ($alpha === null) {
            return new Rgb($red, $green, $blue);
        }

        return new Alpha($alpha, new Rgb($red, $green, $blue));
    }

    protected function createClass(string $method): DataTypeInterface
    {
        $class = $this->formatClass($method);

        if (! class_exists($class)) {
            throw new BadMethodCallException("DataType [{$method}] does not exist.");
        }

        return new $class;
    }

    protected function formatClass(string $method): string
    {
        return 'Devxisas\\LaravelQrCode\\DataTypes\\'.ucfirst($method);
    }

    protected function resolveErrorCorrectionLevel(ErrorCorrection $level): ErrorCorrectionLevel
    {
        $name = $level->value;

        return ErrorCorrectionLevel::$name();
    }
}
