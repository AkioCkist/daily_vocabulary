<?php

// Debug script to test rate limiting behavior
require_once __DIR__ . '/vendor/autoload.php';

echo "=== Rate Limiting Debug ===\n\n";

try {
    $redis = new Redis();
    $redis->connect('127.0.0.1', 6379);
    $redis->select(1);
    echo "✓ Connected to Redis\n\n";
    
    // Test different scenarios
    $testUserId = 999; // Use a test user ID
    $violationKey = 'rate_violations_user_' . $testUserId;
    $globalTimeoutKey = 'rate_global_timeout_user_' . $testUserId;
    
    // Clear existing data
    $redis->del($violationKey);
    $redis->del($globalTimeoutKey);
    
    echo "=== Testing Violation Progression ===\n";
    
    // Test 1: No violations
    $violations = $redis->get($violationKey) ?: 0;
    echo "Current violations: {$violations}\n";
    
    // Test 2: Simulate first violation
    $violations = 1;
    $redis->setex($violationKey, 7 * 24 * 3600, $violations);
    
    // Calculate timeout (from middleware logic)
    $timeouts = [
        0 => 1,    // First timeout: 1 minute  
        1 => 2,    // Second: 2 minutes
        2 => 5,    // Third: 5 minutes
        3 => 10,   // Fourth: 10 minutes
        4 => 15,   // Fifth: 15 minutes
        5 => 30,   // Sixth: 30 minutes
        6 => 60,   // Seventh: 1 hour
        7 => 120,  // Eighth: 2 hours
        8 => 240,  // Ninth: 4 hours
        9 => 480,  // Tenth: 8 hours
    ];
    
    $timeoutMinutes = $timeouts[$violations] ?? 480;
    $timeoutSeconds = $timeoutMinutes * 60;
    
    echo "After {$violations} violation(s): {$timeoutMinutes} minutes ({$timeoutSeconds} seconds)\n";
    
    // Set global timeout
    $expiresAt = date('c', time() + $timeoutSeconds);
    $globalTimeoutData = json_encode([
        'violations' => $violations,
        'expires_at' => $expiresAt,
        'timeout_duration' => $timeoutSeconds
    ]);
    $redis->setex($globalTimeoutKey, $timeoutSeconds, $globalTimeoutData);
    
    echo "Global timeout set: {$timeoutSeconds} seconds\n";
    echo "Expires at: {$expiresAt}\n\n";
    
    // Test 3: Simulate multiple violations
    for ($i = 2; $i <= 5; $i++) {
        $timeoutMinutes = $timeouts[$i] ?? 480;
        $timeoutSeconds = $timeoutMinutes * 60;
        echo "Violation {$i}: {$timeoutMinutes} minutes ({$timeoutSeconds} seconds)\n";
    }
    
    echo "\n=== Current Redis Keys ===\n";
    $keys = $redis->keys('rate_*');
    foreach ($keys as $key) {
        $value = $redis->get($key);
        $ttl = $redis->ttl($key);
        echo "{$key}: {$value} (TTL: {$ttl}s)\n";
    }
    
    echo "\n=== Checking Remaining Time ===\n";
    if ($redis->exists($globalTimeoutKey)) {
        $timeoutData = json_decode($redis->get($globalTimeoutKey), true);
        if ($timeoutData && isset($timeoutData['expires_at'])) {
            $expiresAt = strtotime($timeoutData['expires_at']);
            $remaining = max(0, $expiresAt - time());
            echo "Global timeout remaining: {$remaining} seconds (" . round($remaining/60, 1) . " minutes)\n";
            echo "Should show this timeout on 429 page\n";
        }
    }
    
    $redis->close();
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== Next Steps ===\n";
echo "1. Try triggering rate limit on your web app\n";
echo "2. Check if violations are being stored properly\n";  
echo "3. Verify that global timeout keys are created\n";
echo "4. Make sure the timeout increases with each violation\n";

?>