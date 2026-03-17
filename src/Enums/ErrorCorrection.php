<?php

declare(strict_types=1);

namespace Devxisas\QrStudio\Enums;

enum ErrorCorrection: string
{
    /** 7% data restoration. Smallest QR code. */
    case Low = 'L';

    /** 15% data restoration. Default. */
    case Medium = 'M';

    /** 25% data restoration. Recommended when merging images. */
    case Quartile = 'Q';

    /** 30% data restoration. Best for logos/high-damage scenarios. */
    case High = 'H';
}
