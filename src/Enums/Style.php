<?php

declare(strict_types=1);

namespace Devxisas\LaravelQrCode\Enums;

enum Style: string
{
    case Square = 'square';
    case Dot = 'dot';
    case Round = 'round';
}
