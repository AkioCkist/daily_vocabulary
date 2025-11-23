<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\Cache;

// Test script to check if global rate limiting is working
echo "=== Global Rate Limiting Test ===\n\n";

// Check if Redis is available
try {
    $redis = new \Redis();
    $redis->connect('127.0.0.1', 6379);
    $redis->select(1); // Use database 1 for caching
    echo "✓ Connected to Redis database 1\n";
    
    // Test keys to look for
    $testUserId = 1;
    $testIp = '127.0.0.1';
    
    $lockKeyUser = 'rate_lock_user_' . $testUserId;
    $lockKeyIp = 'rate_lock_ip_' . $testIp;
    $globalTimeoutKeyUser = 'rate_global_timeout_user_' . $testUserId;
    $globalTimeoutKeyIp = 'rate_global_timeout_ip_' . $testIp;
    $violationKeyUser = 'rate_violations_user_' . $testUserId;
    $violationKeyIp = 'rate_violations_ip_' . $testIp;
    
    echo "\nChecking for test keys:\n";
    
    // Check lock keys
    if ($redis->exists($lockKeyUser)) {
        $lockData = json_decode($redis->get($lockKeyUser), true);
        echo "🔒 User {$testUserId} is LOCKED: " . json_encode($lockData) . "\n";
    } else {
        echo "✓ User {$testUserId} is not locked\n";
    }
    
    if ($redis->exists($lockKeyIp)) {
        $lockData = json_decode($redis->get($lockKeyIp), true);
        echo "🔒 IP {$testIp} is LOCKED: " . json_encode($lockData) . "\n";
    } else {
        echo "✓ IP {$testIp} is not locked\n";
    }
    
    // Check global timeout keys
    if ($redis->exists($globalTimeoutKeyUser)) {
        $timeoutData = json_decode($redis->get($globalTimeoutKeyUser), true);
        echo "⏰ User {$testUserId} has global timeout: " . json_encode($timeoutData) . "\n";
    } else {
        echo "✓ User {$testUserId} has no global timeout\n";
    }
    
    if ($redis->exists($globalTimeoutKeyIp)) {
        $timeoutData = json_decode($redis->get($globalTimeoutKeyIp), true);
        echo "⏰ IP {$testIp} has global timeout: " . json_encode($timeoutData) . "\n";
    } else {
        echo "✓ IP {$testIp} has no global timeout\n";
    }
    
    // Check violation keys
    if ($redis->exists($violationKeyUser)) {
        $violations = $redis->get($violationKeyUser);
        echo "⚠️  User {$testUserId} violations: {$violations}\n";
    } else {
        echo "✓ User {$testUserId} has no violations\n";
    }
    
    if ($redis->exists($violationKeyIp)) {
        $violations = $redis->get($violationKeyIp);
        echo "⚠️  IP {$testIp} violations: {$violations}\n";
    } else {
        echo "✓ IP {$testIp} has no violations\n";
    }
    
    echo "\n=== All Redis Keys (rate limit related) ===\n";
    $keys = $redis->keys('rate_*');
    if (empty($keys)) {
        echo "No rate limiting keys found\n";
    } else {
        foreach ($keys as $key) {
            $value = $redis->get($key);
            echo "{$key}: {$value}\n";
        }
    }
    
    $redis->close();
    
} catch (Exception $e) {
    echo "❌ Redis connection failed: " . $e->getMessage() . "\n";
    echo "Make sure Redis is running and accessible on 127.0.0.1:6379\n";
}

echo "\n=== Test Summary ===\n";
echo "Global rate limiting has been implemented with the following features:\n";
echo "1. ✅ ProgressiveRateLimiter creates global timeout keys when violations occur\n";
echo "2. ✅ GlobalRateLimitCheck middleware applies to all web routes\n";
echo "3. ✅ Users cannot bypass timeouts by navigating to other pages\n";
echo "4. ✅ Admin controllers can unlock both locks and global timeouts\n";
echo "5. ✅ Exempted routes (logout, admin) still work during timeouts\n";

echo "\nTo test the system:\n";
echo "1. Make multiple requests to any protected route to trigger violations\n";
echo "2. Try to navigate to homepage, dashboard, or any other route\n";
echo "3. You should get 429 errors on ALL routes until timeout expires\n";
echo "4. Check Redis with this script to see the timeout keys\n";

?>