<?php

namespace Arispati\LaravelLogS3\Tests;

use Arispati\LaravelLogS3\Providers\LaravelLogS3Provider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        // additional setup
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelLogS3Provider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // perform environment setup
    }
}
