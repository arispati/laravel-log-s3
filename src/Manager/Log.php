<?php

namespace Arispati\LaravelLogS3\Manager;

use DateTime;
use DateTimeZone;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Log
{
    protected $enabled = false;
    protected $disk;
    protected $fileName;
    protected $filePath;
    protected $logs;
    protected $timer;
    protected $durations;

    /**
     * Initiate log
     *
     * @param string|null $name
     * @return void
     */
    public function new(?string $name = null): void
    {
        $this->disk = Config::get('logs3.disk');
        $this->fileName = $this->generateFileName($name);
        $this->logs = collect();
        $this->timer = collect();
        $this->durations = collect();
        $this->filePath = $this->getFilePath();
    }

    /**
     * Get file path
     *
     * @return string
     */
    public function getFilePath(): string
    {
        return sprintf(
            '%s/%s',
            Config::get('logs3.path'),
            $this->getdateTime()->format('Y-m-d')
        );
    }

    /**
     * Enabled/Disabled log
     *
     * @param boolean $enabled
     * @return void
     */
    public function enabled(bool $enabled = true): void
    {
        $this->enabled = $enabled;
    }

    /**
     * Debug
     *
     * @param mixed $message
     * @return void
     */
    public function debug($message): void
    {
        if ($this->enabled) {
            $this->logs->push($this->formatMessage($message));
        }
    }

    /**
     * Debug with duration
     *
     * @param string $message
     * @param string $timer
     * @return void
     */
    public function debugDuration(string $message, string $timer): void
    {
        if ($this->enabled) {
            $this->logs->push(sprintf(
                '%s (%s)',
                $this->formatMessage($message),
                $this->duration($timer)
            ));
        }
    }

    /**
     * Start the timer
     *
     * @param string $name
     * @return void
     */
    public function timer(string $name = 'default'): void
    {
        if ($this->enabled) {
            $this->timer->put(Str::slug($name), microtime(true));
        }
    }

    /**
     * Get duration of timer
     *
     * @param string $name
     * @return float
     */
    public function duration(string $name = 'default'): float
    {
        if ($this->enabled) {
            $key = Str::slug($name);

            if ($thetimer = $this->timer->get($key)) {
                $endAt = (microtime(true) - $thetimer);
            } else {
                $endAt = 0;
            }

            $this->durations->put($key, $endAt);

            return $endAt;
        }

        return 0;
    }

    /**
     * Get all available timers duration
     *
     * @return null|Collection
     */
    public function getDurations(): ?Collection
    {
        return $this->durations;
    }

    /**
     * Write the logs
     *
     * @return void
     */
    public function write(): void
    {
        if ($this->enabled) {
            try {
                $file = sprintf('%s/%s', $this->filePath, $this->fileName);
                Storage::disk($this->disk)->put($file, $this->logs->join("\n"));
            } catch (\Exception $e) {
                // do nothing
            }
        }
    }

    /**
     * Generate filename
     *
     * @param string|null $name
     * @return string
     */
    private function generateFileName(?string $name): string
    {
        return sprintf(
            '%s_%s_%s.log',
            time(),
            $this->getdateTime()->format('His'),
            $name ?? Str::random(5)
        );
    }

    /**
     * Format the parameters for the logger.
     *
     * @param  mixed  $message
     * @return mixed
     */
    private function formatMessage($message)
    {
        if (is_array($message)) {
            $message = var_export($message, true);
        } elseif ($message instanceof Jsonable) {
            $message = $message->toJson();
        } elseif ($message instanceof Arrayable) {
            $message = var_export($message->toArray(), true);
        }

        return sprintf(
            '[%s] %s',
            $this->getdateTime()->format('Y-m-d H:i:s'),
            $message
        );
    }

    /**
     * Get date time
     *
     * @return DateTime
     */
    private function getdateTime(): DateTime
    {
        return new DateTime('now', new DateTimeZone(Config::get('logs3.timezone')));
    }
}
