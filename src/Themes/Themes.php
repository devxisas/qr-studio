<?php

declare(strict_types=1);

namespace Devxisas\QrStudio\Themes;

use InvalidArgumentException;

/**
 * Built-in visual themes for QR Studio.
 *
 * Each theme is a named preset that combines colors, gradient, module style,
 * and eye style into a single, scannable visual identity.
 *
 * Available themes: ocean, sunset, forest, midnight, coral
 *
 * Usage:
 *   QrCode::theme('ocean')->generate('...')
 *   QrCode::theme(Theme::Sunset)->generate('...')
 */
class Themes
{
    /**
     * Returns the settings array for a given theme name.
     *
     * @return array<string, mixed>
     *
     * @throws InvalidArgumentException if the theme does not exist
     */
    public static function get(string $name): array
    {
        return match ($name) {
            'ocean'    => self::ocean(),
            'sunset'   => self::sunset(),
            'forest'   => self::forest(),
            'midnight' => self::midnight(),
            'coral'    => self::coral(),
            default    => throw new InvalidArgumentException(
                "Theme [{$name}] does not exist. Available: ".implode(', ', self::available()).'.'
            ),
        };
    }

    /** @return string[] */
    public static function available(): array
    {
        return ['ocean', 'sunset', 'forest', 'midnight', 'coral'];
    }

    /** Deep blue → cyan radial gradient, circle eyes, dot modules. */
    private static function ocean(): array
    {
        return [
            'gradient'        => [0, 119, 190, 0, 180, 216, 'radial'],
            'eye'             => 'circle',
            'style'           => ['dot', 0.6],
            'errorCorrection' => 'H',
        ];
    }

    /** Orange → crimson diagonal gradient, square eyes, default modules. */
    private static function sunset(): array
    {
        return [
            'gradient' => [255, 82, 0, 214, 0, 110, 'diagonal'],
            'eye'      => 'square',
        ];
    }

    /** Dark green → mint vertical gradient, square eyes, round modules. */
    private static function forest(): array
    {
        return [
            'gradient'        => [22, 101, 52, 74, 222, 128, 'vertical'],
            'eye'             => 'square',
            'style'           => ['round', 0.6],
            'errorCorrection' => 'H',
        ];
    }

    /** Light blue-white on deep navy — minimal and high-contrast. */
    private static function midnight(): array
    {
        return [
            'color'           => [214, 230, 255],
            'backgroundColor' => [8, 10, 40],
            'eye'             => 'square',
        ];
    }

    /** Coral → hot-pink horizontal gradient, circle eyes, dot modules. */
    private static function coral(): array
    {
        return [
            'gradient'        => [255, 107, 53, 252, 63, 135, 'horizontal'],
            'eye'             => 'circle',
            'style'           => ['dot', 0.55],
            'errorCorrection' => 'H',
        ];
    }
}
