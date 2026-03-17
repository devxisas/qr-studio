<?php

declare(strict_types=1);

namespace Devxisas\QrStudio\DataTypes;

class PhoneNumber implements DataTypeInterface
{
    protected string $prefix = 'tel:';

    protected string $phoneNumber = '';

    /** @param  array<int, mixed>  $arguments */
    public function create(array $arguments): void
    {
        $this->phoneNumber = (string) ($arguments[0] ?? '');
    }

    public function __toString(): string
    {
        return $this->prefix.$this->phoneNumber;
    }
}
