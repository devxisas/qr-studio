<?php

declare(strict_types=1);

namespace Devxisas\QrStudio\Tests;

use Devxisas\QrStudio\Facades\QrCode;
use Devxisas\QrStudio\QrStudioServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            QrStudioServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'QrCode' => QrCode::class,
        ];
    }
}
