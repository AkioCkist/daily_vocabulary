<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\RateLimiter;

require_once 'vendor/autoload.php';

try {
    $app = require_once 'bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $kernel->bootstrap();
    
    // Simulate rate limiting
    echo "Testing rate limiting...\n";
    
    $key = 'test_rate_limit_key';
    $limit = 5;
    
    // Hit the rate limiter multiple times
    for ($i = 1; $i <= 7; $i++) {
        $hit = RateLimiter::hit($key, 60); // 60 seconds decay
        $remaining = RateLimiter::remaining($key, $limit);
        $tooMany = RateLimiter::tooManyAttempts($key, $limit);
        
        echo "Attempt $i: Hits=$hit, Remaining=$remaining, TooMany=" . ($tooMany ? 'YES' : 'NO') . "\n";
        
        if ($tooMany) {
            $availableIn = RateLimiter::availableIn($key);
            echo "Rate limited! Available in: $availableIn seconds\n";
            break;
        }
    }
    
    echo "\nNow check Redis with: KEYS *laravel* or KEYS *rate* or KEYS *throttle*\n";
    
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}