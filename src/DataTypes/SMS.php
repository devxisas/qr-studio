<?php

declare(strict_types=1);

namespace Devxisas\QrStudio\DataTypes;

class SMS implements DataTypeInterface
{
    protected string $prefix = 'sms:';

    protected string $phoneNumber = '';

    protected string $message = '';

    /** @param  array<int, mixed>  $arguments */
    public function create(array $arguments): void
    {
        $this->phoneNumber = (string) ($arguments[0] ?? '');
        $this->message = (string) ($arguments[1] ?? '');
    }

    public function __toString(): string
    {
        $sms = $this->prefix.$this->phoneNumber;

        if ($this->message !== '') {
            $sms .= '&body='.$this->message;
        }

        return $sms;
    }
}
