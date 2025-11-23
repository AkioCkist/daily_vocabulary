<?php

use Illuminate\Foundation\Application;

require_once 'vendor/autoload.php';

try {
    $app = require_once 'bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $kernel->bootstrap();
    
    // Test Laravel's cache
    $cache = app('cache');
    echo 'Laravel cache store: ' . get_class($cache->getStore()) . PHP_EOL;
    
    // Test cache functionality
    $cache->put('laravel_test_key', 'laravel_test_value', 60);
    $value = $cache->get('laravel_test_key');
    echo 'Laravel cache test: ' . ($value === 'laravel_test_value' ? 'SUCCESS' : 'FAILED') . PHP_EOL;
    
    echo 'Rate limiting should now store data in Redis!' . PHP_EOL;
    
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}