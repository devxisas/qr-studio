<?php

declare(strict_types=1);

namespace Devxisas\LaravelQrCode\Tests;

use Devxisas\LaravelQrCode\Facades\QrCode;
use Devxisas\LaravelQrCode\LaravelQrCodeServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            LaravelQrCodeServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'QrCode' => QrCode::class,
        ];
    }
}
