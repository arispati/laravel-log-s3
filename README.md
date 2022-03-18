# laravel-log-s3
Laravel library for logging and store it to s3

### Requirement
- PHP >= 7.2
- Laravel >= 5.6

### Installation
- Install with composer
```bash
composer require arispati/laravel-log-s3
```

### Export Configuration
```bash
php artisan vendor:publish --provider="Arispati\LaravelLogS3\Providers\LaravelLogS3Provider" --tag="config"
```
```php
<?php

return [
    'disk' => 's3',
    'path' => 'logs',
    'timezone' => 'Asia/Jakarta'
];
```

### Usage
```php
use Arispati\LaravelLogS3\Facades\Log;

Log::new('new-log');
Log::enabled(true);
Log::debug('Log Start');
Log::timer('log-start');
usleep(500000);
Log::debugDuration('Log End', 'log-start');
Log::write();
```