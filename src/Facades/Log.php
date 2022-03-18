<?php

namespace Arispati\LaravelLogS3\Facades;

use Arispati\LaravelLogS3\Manager\Log as ManagerLog;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void new(?string $name = null)
 * @method static void enabled(bool $enabled = true)
 * @method static void debug(mixed $message)
 * @method static void debugDuration(string $message, string $timer): void
 * @method static void timer(string $name = 'default')
 * @method static float duration(string $name = 'default')
 * @method static ?Illuminate\Support\Collection getDurations()
 * @method static void write()
 */
class Log extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return ManagerLog::class;
    }
}
