<?php

declare(strict_types=1);

namespace Devxisas\QrStudio\DataTypes;

class Geo implements DataTypeInterface
{
    protected string $prefix = 'geo:';

    protected float $latitude = 0.0;

    protected float $longitude = 0.0;

    /** @param  array<int, mixed>  $arguments */
    public function create(array $arguments): void
    {
        $this->latitude = (float) ($arguments[0] ?? 0.0);
        $this->longitude = (float) ($arguments[1] ?? 0.0);
    }

    public function __toString(): string
    {
        return $this->prefix.$this->latitude.','.$this->longitude;
    }
}
