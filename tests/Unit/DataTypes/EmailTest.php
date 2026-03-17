<?php

declare(strict_types=1);

use Devxisas\LaravelQrCode\DataTypes\Email;

it('generates a basic mailto uri', function () {
    $email = new Email;
    $email->create(['test@example.com']);

    expect((string) $email)->toBe('mailto:test@example.com');
});

it('generates a mailto uri with subject and body', function () {
    $email = new Email;
    $email->create(['test@example.com', 'Hello', 'World']);

    expect((string) $email)->toBe('mailto:test@example.com?subject=Hello&body=World');
});

it('throws an exception for an invalid email', function () {
    $email = new Email;
    $email->create(['not-an-email']);
})->throws(InvalidArgumentException::class);
