<?php

declare(strict_types=1);

namespace Devxisas\QrStudio\Enums;

enum Format: string
{
    case Svg = 'svg';
    case Eps = 'eps';
    case Png = 'png';

    public function mimeType(): string
    {
        return match ($this) {
            self::Svg => 'image/svg+xml',
            self::Eps => 'application/postscript',
            self::Png => 'image/png',
        };
    }

    public function dataUriPrefix(): string
    {
        return 'data:'.$this->mimeType().';base64,';
    }
}
