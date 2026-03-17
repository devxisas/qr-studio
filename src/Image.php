<?php

declare(strict_types=1);

namespace Devxisas\QrStudio;

use GdImage;
use InvalidArgumentException;

class Image
{
    protected GdImage $image;

    public function __construct(string $imageData)
    {
        $resource = @imagecreatefromstring($imageData);

        if ($resource === false) {
            throw new InvalidArgumentException('Could not create image from the provided string.');
        }

        $this->image = $resource;
    }

    public function getWidth(): int
    {
        return imagesx($this->image);
    }

    public function getHeight(): int
    {
        return imagesy($this->image);
    }

    public function getImageResource(): GdImage
    {
        return $this->image;
    }

    public function setImageResource(GdImage $image): void
    {
        $this->image = $image;
    }
}
