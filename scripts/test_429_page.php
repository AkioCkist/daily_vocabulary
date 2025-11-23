<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

require_once 'vendor/autoload.php';

try {
    $app = require_once 'bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $kernel->bootstrap();
    
    echo "Testing 429 error page...\n";
    
    // Simulate a rate limit hit
    $key = 'test_429_error';
    $limit = 1;
    
    // Hit the limit
    RateLimiter::hit($key, 60);
    RateLimiter::hit($key, 60); // This should exceed the limit
    
    if (RateLimiter::tooManyAttempts($key, $limit)) {
        $retryAfter = RateLimiter::availableIn($key);
        echo "Rate limit exceeded! Retry after: $retryAfter seconds\n";
        echo "Visit your Laravel app and trigger a rate limit to see the 429 error page.\n";
    }
    
    // Clear the test key
    RateLimiter::clear($key);
    
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}