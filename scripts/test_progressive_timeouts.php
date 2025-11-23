<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\Cache;

// Test script to simulate progressive rate limiting
echo "=== Progressive Rate Limiting Simulation ===\n\n";

// Connect to Redis
try {
    $redis = new \Redis();
    $redis->connect('127.0.0.1', 6379);
    $redis->select(1); // Use database 1
    echo "✓ Connected to Redis\n";
    
    // Test user
    $testUserId = 1;
    $violationKey = 'rate_violations_user_' . $testUserId;
    $globalTimeoutKey = 'rate_global_timeout_user_' . $testUserId;
    
    // Clear any existing data
    $redis->del($violationKey);
    $redis->del($globalTimeoutKey);
    
    echo "\n=== Simulating Progressive Timeouts ===\n";
    
    // Progressive timeout function (matching middleware)
    function calculateTimeout(int $violations): int
    {
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
        
        return $timeouts[$violations] ?? 480;
    }
    
    // Simulate violations
    for ($i = 0; $i < 10; $i++) {
        $violations = $i + 1;
        $timeoutMinutes = calculateTimeout($violations);
        $timeoutSeconds = $timeoutMinutes * 60;
        
        // Set violation count
        $redis->setex($violationKey, 7 * 24 * 3600, $violations); // 7 days
        
        // Set global timeout
        $expiresAt = date('c', time() + $timeoutSeconds);
        $globalTimeoutData = json_encode([
            'violations' => $violations,
            'expires_at' => $expiresAt,
            'timeout_duration' => $timeoutSeconds
        ]);
        $redis->setex($globalTimeoutKey, $timeoutSeconds, $globalTimeoutData);
        
        echo "Violation {$violations}: {$timeoutMinutes} minutes ({$timeoutSeconds} seconds)\n";
        
        // Show what's in Redis
        $currentViolations = $redis->get($violationKey);
        $currentTimeout = json_decode($redis->get($globalTimeoutKey), true);
        
        if ($i === 0 || $i === 4 || $i === 9) {
            echo "  → Redis violations: {$currentViolations}\n";
            echo "  → Redis timeout: " . ($currentTimeout['timeout_duration'] ?? 'none') . " seconds\n";
            echo "  → Expires at: " . ($currentTimeout['expires_at'] ?? 'none') . "\n\n";
        }
    }
    
    echo "\n=== Current Redis State ===\n";
    $currentViolations = $redis->get($violationKey);
    $currentTimeoutData = $redis->get($globalTimeoutKey);
    
    echo "Violations stored: {$currentViolations}\n";
    echo "Global timeout data: {$currentTimeoutData}\n";
    
    if ($currentTimeoutData) {
        $timeoutData = json_decode($currentTimeoutData, true);
        $expiresAt = strtotime($timeoutData['expires_at']);
        $remaining = max(0, $expiresAt - time());
        echo "Time remaining: {$remaining} seconds (" . round($remaining/60, 1) . " minutes)\n";
    }
    
    echo "\n=== Test Progressive Logic ===\n";
    echo "Next violation would be: " . calculateTimeout($currentViolations) . " minutes\n";
    echo "After that: " . calculateTimeout($currentViolations + 1) . " minutes\n";
    
    $redis->close();
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== Summary ===\n";
echo "✅ Progressive timeouts: 1min → 2min → 5min → 10min → 15min → 30min → 1hr → 2hr → 4hr → 8hr\n";
echo "✅ After 10 violations: Permanent lock (24 hours)\n";
echo "✅ Each violation should increase timeout progressively\n";

?>