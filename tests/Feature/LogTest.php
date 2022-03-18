<?php

namespace Arispati\LaravelLogS3\Tests\Feature;

use Arispati\LaravelLogS3\Facades\Log;
use Arispati\LaravelLogS3\Manager\Log as ManagerLog;
use Arispati\LaravelLogS3\Tests\TestCase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

class LogTest extends TestCase
{
    /** @test */
    public function writeLogTest()
    {
        Log::new('new-log');
        Log::enabled(true);
        Log::debug('Log Start');
        Log::timer('log-start');
        usleep(500000);
        Log::debugDuration('Log End', 'log-start');

        $log = app(ManagerLog::class);
        $rc = new ReflectionClass($log);
        $logs = $rc->getProperty('logs');
        $disk = $rc->getProperty('disk');
        $enabled = $rc->getProperty('enabled');
        $logs->setAccessible(true);
        $disk->setAccessible(true);
        $enabled->setAccessible(true);

        $this->assertSame(2, count($logs->getValue($log)));
        $this->assertSame('s3', $disk->getValue($log));
        $this->assertSame(true, $enabled->getValue($log));
    }

    /** @test */
    public function writeLogDisabledTest()
    {
        Log::new('new-log');
        Log::debug('Log Start');

        $log = app(ManagerLog::class);
        $rc = new ReflectionClass($log);
        $logs = $rc->getProperty('logs');
        $enabled = $rc->getProperty('enabled');
        $logs->setAccessible(true);
        $enabled->setAccessible(true);

        $this->assertSame(0, count($logs->getValue($log)));
        $this->assertSame(false, $enabled->getValue($log));
    }

    /** @test */
    public function configDiskTest()
    {
        Config::set('logs3.disk', 'local');
        Log::new('new-log');
        Log::debug('Log Start');

        $log = app(ManagerLog::class);
        $rc = new ReflectionClass($log);
        $disk = $rc->getProperty('disk');
        $disk->setAccessible(true);

        $this->assertSame('local', $disk->getValue($log));
    }

    /** @test */
    public function writeFileTest()
    {
        Config::set('logs3.disk', 'local');
        Log::new('new-log');
        Log::enabled(true);
        Log::debug('Log Start');
        Log::write();

        $log = app(ManagerLog::class);
        $rc = new ReflectionClass($log);
        $path = $rc->getProperty('filePath');
        $path->setAccessible(true);
        $name = $rc->getProperty('fileName');
        $name->setAccessible(true);

        $file = sprintf(
            '%s/%s',
            $path->getValue($log),
            $name->getValue($log)
        );

        Storage::disk('local')->assertExists($file);
    }
}
