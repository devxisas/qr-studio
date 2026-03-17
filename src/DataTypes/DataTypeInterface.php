<?php

declare(strict_types=1);

namespace Devxisas\QrStudio\DataTypes;

interface DataTypeInterface
{
    /**
     * Initializes the DataType object from the given arguments.
     *
     * @param  array<int, mixed>  $arguments
     */
    public function create(array $arguments): void;

    /**
     * Returns the formatted QR code string for this data type.
     */
    public function __toString(): string;
}
