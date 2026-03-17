<?php

declare(strict_types=1);

namespace Devxisas\QrStudio\Enums;

enum GradientType: string
{
    case Horizontal = 'horizontal';
    case Vertical = 'vertical';
    case Diagonal = 'diagonal';
    case InverseDiagonal = 'inverse_diagonal';
    case Radial = 'radial';

    /** Returns the value formatted for BaconQrCode's GradientType static call. */
    public function toBaconType(): string
    {
        return strtoupper($this->value);
    }
}
