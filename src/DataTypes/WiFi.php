<?php

declare(strict_types=1);

namespace Devxisas\QrStudio\DataTypes;

class WiFi implements DataTypeInterface
{
    protected string $prefix = 'WIFI:';

    protected string $separator = ';';

    protected string $encryption = '';

    protected string $ssid = '';

    protected string $password = '';

    protected bool $hidden = false;

    protected bool $hasHidden = false;

    /** @param  array<int, mixed>  $arguments */
    public function create(array $arguments): void
    {
        $options = $arguments[0] ?? [];

        if (! is_array($options)) {
            return;
        }

        $this->encryption = (string) ($options['encryption'] ?? '');
        $this->ssid = (string) ($options['ssid'] ?? '');
        $this->password = (string) ($options['password'] ?? '');

        if (isset($options['hidden'])) {
            $this->hidden = (bool) $options['hidden'];
            $this->hasHidden = true;
        }
    }

    public function __toString(): string
    {
        $wifi = $this->prefix;

        if ($this->encryption !== '') {
            $wifi .= 'T:'.$this->encryption.$this->separator;
        }
        if ($this->ssid !== '') {
            $wifi .= 'S:'.$this->ssid.$this->separator;
        }
        if ($this->password !== '') {
            $wifi .= 'P:'.$this->password.$this->separator;
        }
        if ($this->hasHidden) {
            $wifi .= 'H:'.($this->hidden ? 'true' : 'false').$this->separator;
        }

        return $wifi;
    }
}
