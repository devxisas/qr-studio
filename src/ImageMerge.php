<?php

declare(strict_types=1);

namespace Devxisas\QrStudio;

use InvalidArgumentException;

class ImageMerge
{
    protected int $sourceImageHeight;

    protected int $sourceImageWidth;

    protected int $mergeImageHeight;

    protected int $mergeImageWidth;

    protected float $mergeRatio;

    protected int $postMergeImageHeight;

    protected int $postMergeImageWidth;

    protected int $centerX;

    protected int $centerY;

    public function __construct(
        protected Image $sourceImage,
        protected Image $mergeImage,
    ) {}

    public function merge(float $percentage): string
    {
        $this->setProperties($percentage);

        $img = imagecreatetruecolor($this->sourceImage->getWidth(), $this->sourceImage->getHeight());

        if ($img === false) {
            throw new \RuntimeException('Could not create image canvas.');
        }

        imagealphablending($img, true);
        $transparent = imagecolorallocatealpha($img, 0, 0, 0, 127);

        if ($transparent !== false) {
            imagefill($img, 0, 0, $transparent);
        }

        imagecopy(
            $img,
            $this->sourceImage->getImageResource(),
            0,
            0,
            0,
            0,
            $this->sourceImage->getWidth(),
            $this->sourceImage->getHeight()
        );

        imagecopyresampled(
            $img,
            $this->mergeImage->getImageResource(),
            $this->centerX,
            $this->centerY,
            0,
            0,
            $this->postMergeImageWidth,
            $this->postMergeImageHeight,
            $this->mergeImageWidth,
            $this->mergeImageHeight
        );

        $this->sourceImage->setImageResource($img);

        return $this->createImage();
    }

    protected function createImage(): string
    {
        ob_start();
        imagepng($this->sourceImage->getImageResource());

        return (string) ob_get_clean();
    }

    protected function setProperties(float $percentage): void
    {
        if ($percentage > 1) {
            throw new InvalidArgumentException('$percentage must be less than or equal to 1.');
        }

        $this->sourceImageHeight = $this->sourceImage->getHeight();
        $this->sourceImageWidth = $this->sourceImage->getWidth();
        $this->mergeImageHeight = $this->mergeImage->getHeight();
        $this->mergeImageWidth = $this->mergeImage->getWidth();

        $this->calculateOverlap($percentage);
        $this->calculateCenter();
    }

    protected function calculateCenter(): void
    {
        $this->centerX = intval(($this->sourceImageWidth / 2) - ($this->postMergeImageWidth / 2));
        $this->centerY = intval(($this->sourceImageHeight / 2) - ($this->postMergeImageHeight / 2));
    }

    protected function calculateOverlap(float $percentage): void
    {
        $this->mergeRatio = round($this->mergeImageWidth / $this->mergeImageHeight, 2);
        $this->postMergeImageWidth = intval($this->sourceImageWidth * $percentage);
        $this->postMergeImageHeight = intval($this->postMergeImageWidth / $this->mergeRatio);
    }
}
