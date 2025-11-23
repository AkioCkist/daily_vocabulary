<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

try {
    // Test cache connection
    $cache = $app->make('cache');
    echo "Cache store: " . get_class($cache->getStore()) . PHP_EOL;
    
    // Test putting and getting from cache
    $cache->put('test_key', 'test_value', 60);
    $value = $cache->get('test_key');
    echo "Cache test: " . ($value === 'test_value' ? 'SUCCESS' : 'FAILED') . PHP_EOL;
    
    // Check rate limiter specifically
    $rateLimiter = $app->make('Illuminate\Cache\RateLimiter');
    echo "Rate limiter class: " . get_class($rateLimiter) . PHP_EOL;
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}