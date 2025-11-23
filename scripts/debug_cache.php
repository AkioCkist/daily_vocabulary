<?php

use Illuminate\Foundation\Application;

require_once 'vendor/autoload.php';

try {
    $app = require_once 'bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $kernel->bootstrap();
    
    $cache = app('cache');
    echo "Current cache store: " . get_class($cache->getStore()) . "\n";
    
    $config = app('config');
    echo "Cache default from config: " . $config->get('cache.default') . "\n";
    echo "CACHE_DRIVER env: " . env('CACHE_DRIVER') . "\n";
    echo "CACHE_STORE env: " . env('CACHE_STORE') . "\n";
    
    // Try to put something in cache and see where it goes
    $cache->put('debug_test_key', 'debug_test_value', 60);
    echo "Stored test key in cache\n";
    
    // Check if it's in Redis
    $redis = new Redis();
    $redis->connect('127.0.0.1', 6379);
    $allKeys = $redis->keys('*');
    echo "Redis keys after cache put: " . count($allKeys) . " keys\n";
    foreach ($allKeys as $key) {
        echo "  - $key\n";
    }
    $redis->close();
    
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}