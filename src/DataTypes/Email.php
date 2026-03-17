<?php

declare(strict_types=1);

namespace Devxisas\QrStudio\DataTypes;

use InvalidArgumentException;

class Email implements DataTypeInterface
{
    protected string $prefix = 'mailto:';

    protected string $email = '';

    protected string $subject = '';

    protected string $body = '';

    /** @param  array<int, mixed>  $arguments */
    public function create(array $arguments): void
    {
        $email = (string) ($arguments[0] ?? '');

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid email address provided: [{$email}].");
        }

        $this->email = $email;
        $this->subject = (string) ($arguments[1] ?? '');
        $this->body = (string) ($arguments[2] ?? '');
    }

    public function __toString(): string
    {
        $uri = $this->prefix.$this->email;

        $params = array_filter([
            'subject' => $this->subject,
            'body' => $this->body,
        ]);

        if ($params !== []) {
            $uri .= '?'.http_build_query($params);
        }

        return $uri;
    }
}
