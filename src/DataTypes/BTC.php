<?php

declare(strict_types=1);

namespace Devxisas\QrStudio\DataTypes;

class BTC implements DataTypeInterface
{
    protected string $prefix = 'bitcoin:';

    protected string $address = '';

    protected string $amount = '';

    protected string $label = '';

    protected string $message = '';

    protected string $returnAddress = '';

    /** @param  array<int, mixed>  $arguments */
    public function create(array $arguments): void
    {
        $this->address = (string) ($arguments[0] ?? '');
        $this->amount = (string) ($arguments[1] ?? '');

        if (isset($arguments[2]) && is_array($arguments[2])) {
            $options = $arguments[2];
            $this->label = (string) ($options['label'] ?? '');
            $this->message = (string) ($options['message'] ?? '');
            $this->returnAddress = (string) ($options['returnAddress'] ?? '');
        }
    }

    public function __toString(): string
    {
        $query = http_build_query(array_filter([
            'amount' => $this->amount,
            'label' => $this->label,
            'message' => $this->message,
            'r' => $this->returnAddress,
        ]));

        $uri = $this->prefix.$this->address;

        if ($query !== '') {
            $uri .= '?'.$query;
        }

        return $uri;
    }
}
