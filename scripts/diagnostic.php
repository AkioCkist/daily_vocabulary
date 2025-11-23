<?php

/**
 * Comprehensive Rate Limit Diagnostic Script
 * This will help identify what's causing the ongoing rate limit issues
 */

require_once __DIR__ . '/vendor/autoload.php';

echo "=== Rate Limit Diagnostic & Debug ===\n\n";

try {
    $redis = new Redis();
    $redis->connect('127.0.0.1', 6379);
    $redis->select(1);
    echo "✓ Connected to Redis database 1\n\n";
    
    // Get your current IP for testing
    $currentIP = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    echo "🌐 Current IP: {$currentIP}\n\n";
    
    echo "=== SCANNING ALL REDIS DATABASES ===\n";
    
    // Check multiple Redis databases (rate limits might be in different DBs)
    for ($db = 0; $db <= 15; $db++) {
        $redis->select($db);
        $keys = $redis->keys('*rate*');
        $authKeys = $redis->keys('*auth*');
        $cacheKeys = $redis->keys('*cache*');
        $laravelKeys = $redis->keys('*laravel*');
        $throttleKeys = $redis->keys('*throttle*');
        $limitKeys = $redis->keys('*limit*');
        
        $allKeys = array_unique(array_merge($keys, $authKeys, $cacheKeys, $laravelKeys, $throttleKeys, $limitKeys));
        
        if (!empty($allKeys)) {
            echo "📊 Database {$db}: " . count($allKeys) . " relevant keys found\n";
            foreach ($allKeys as $key) {
                $ttl = $redis->ttl($key);
                $value = $redis->get($key);
                $ttlInfo = $ttl > 0 ? "(TTL: {$ttl}s)" : "(No expiry)";
                
                // Truncate long values
                $displayValue = strlen($value) > 100 ? substr($value, 0, 100) . '...' : $value;
                
                echo "   🔑 {$key}: {$displayValue} {$ttlInfo}\n";
                
                // If this looks like it might be blocking current IP
                if (str_contains($key, $currentIP) || str_contains($key, '127.0.0.1')) {
                    echo "      ⚠️  This key contains your IP!\n";
                }
            }
            echo "\n";
        }
    }
    
    // Switch back to database 1
    $redis->select(1);
    
    echo "=== SPECIFIC IP ANALYSIS ===\n";
    $testIPs = [$currentIP, '127.0.0.1', '::1', 'localhost'];
    
    foreach ($testIPs as $ip) {
        echo "🔍 Analyzing IP: {$ip}\n";
        
        // Check all possible key patterns
        $patterns = [
            "*{$ip}*",
            "rate_*{$ip}*",
            "auth_*{$ip}*",
            "throttle*{$ip}*",
            "limit*{$ip}*",
            "*rate*{$ip}*"
        ];
        
        $found = false;
        foreach ($patterns as $pattern) {
            $keys = $redis->keys($pattern);
            foreach ($keys as $key) {
                if (!$found) {
                    echo "   📍 Found keys for {$ip}:\n";
                    $found = true;
                }
                $ttl = $redis->ttl($key);
                $value = $redis->get($key);
                echo "      🔑 {$key} = {$value} (TTL: {$ttl}s)\n";
            }
        }
        
        if (!$found) {
            echo "   ✅ No keys found for {$ip}\n";
        }
        echo "\n";
    }
    
    echo "=== LARAVEL CACHE INVESTIGATION ===\n";
    
    // Check Laravel's cache structure
    $laravelPrefixes = [
        'laravel_cache:',
        'daily_vocabulary_cache:',
        'cache:',
        'app_cache:',
        ''
    ];
    
    foreach ($laravelPrefixes as $prefix) {
        $cacheKeys = $redis->keys($prefix . '*rate*');
        $cacheKeys = array_merge($cacheKeys, $redis->keys($prefix . '*throttle*'));
        $cacheKeys = array_merge($cacheKeys, $redis->keys($prefix . '*limit*'));
        
        if (!empty($cacheKeys)) {
            echo "📦 Laravel cache with prefix '{$prefix}': " . count($cacheKeys) . " keys\n";
            foreach ($cacheKeys as $key) {
                $ttl = $redis->ttl($key);
                $value = $redis->get($key);
                echo "   🔑 {$key} = {$value} (TTL: {$ttl}s)\n";
            }
            echo "\n";
        }
    }
    
    echo "=== FORTIFY RATE LIMITER CHECK ===\n";
    
    // Check Fortify rate limiter keys
    $fortifyKeys = $redis->keys('*login*');
    $fortifyKeys = array_merge($fortifyKeys, $redis->keys('*fortify*'));
    $fortifyKeys = array_merge($fortifyKeys, $redis->keys('*two-factor*'));
    
    if (!empty($fortifyKeys)) {
        echo "🔐 Fortify keys found: " . count($fortifyKeys) . "\n";
        foreach ($fortifyKeys as $key) {
            $ttl = $redis->ttl($key);
            $value = $redis->get($key);
            echo "   🔑 {$key} = {$value} (TTL: {$ttl}s)\n";
        }
    } else {
        echo "✅ No Fortify keys found\n";
    }
    
    echo "\n=== SUSPECTED RATE LIMIT SOURCES ===\n";
    
    // Look for keys that might be causing the issue
    $suspiciousPatterns = [
        '*throttle*',
        '*rate_limit*',
        '*rate-limit*',
        '*rateLimit*',
        '*too_many*',
        '*429*'
    ];
    
    $suspiciousKeys = [];
    foreach ($suspiciousPatterns as $pattern) {
        $keys = $redis->keys($pattern);
        $suspiciousKeys = array_merge($suspiciousKeys, $keys);
    }
    
    $suspiciousKeys = array_unique($suspiciousKeys);
    
    if (!empty($suspiciousKeys)) {
        echo "⚠️  Found " . count($suspiciousKeys) . " suspicious keys that might be causing rate limiting:\n";
        foreach ($suspiciousKeys as $key) {
            $ttl = $redis->ttl($key);
            $value = $redis->get($key);
            echo "   🚨 {$key} = {$value} (TTL: {$ttl}s)\n";
            
            // Auto-remove if you want
            // $redis->del($key);
        }
        echo "\n";
    } else {
        echo "✅ No suspicious rate limiting keys found\n\n";
    }
    
    echo "=== NUCLEAR CLEANUP OPTION ===\n";
    echo "If you want to remove ALL found keys, run:\n";
    echo "php diagnostic.php --nuclear-cleanup\n\n";
    
    // Nuclear cleanup option
    if (isset($argv[1]) && $argv[1] === '--nuclear-cleanup') {
        echo "💥 NUCLEAR CLEANUP ACTIVATED\n";
        
        $allDatabases = [];
        for ($db = 0; $db <= 15; $db++) {
            $redis->select($db);
            $patterns = ['*rate*', '*auth*', '*throttle*', '*limit*', '*cache*'];
            
            foreach ($patterns as $pattern) {
                $keys = $redis->keys($pattern);
                foreach ($keys as $key) {
                    $allDatabases[] = "DB{$db}: {$key}";
                    $redis->del($key);
                }
            }
        }
        
        echo "🧹 Removed " . count($allDatabases) . " keys across all Redis databases\n";
        foreach ($allDatabases as $key) {
            echo "   🗑️  {$key}\n";
        }
    }
    
    $redis->close();
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== NEXT STEPS ===\n";
echo "1. If suspicious keys were found above, they might be causing the issue\n";
echo "2. Try: php diagnostic.php --nuclear-cleanup\n";
echo "3. Check if rate limiting is happening at web server level (Nginx/Apache)\n";
echo "4. Verify Laravel's cache configuration in config/cache.php\n";
echo "5. Check if there are other rate limiting middlewares active\n";

?>