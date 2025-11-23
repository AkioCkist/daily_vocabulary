<?php

/**
 * Rate Limit Management Script
 * Usage: php unrestrict.php [all|ip] [ip_address]
 * 
 * Examples:
 * php unrestrict.php all                    - Remove all rate limits and locks
 * php unrestrict.php ip 192.168.1.100      - Remove rate limits for specific IP
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\Cache;

// Parse command line arguments
$action = $argv[1] ?? 'help';
$ipAddress = $argv[2] ?? null;

echo "=== Rate Limit Management Script ===\n\n";

try {
    $redis = new Redis();
    $redis->connect('127.0.0.1', 6379);
    $redis->select(1); // Use database 1 for caching
    echo "✓ Connected to Redis database 1\n\n";
    
    switch ($action) {
        case 'all':
            unrestrict_all($redis);
            break;
            
        case 'ip':
            if (!$ipAddress) {
                echo "❌ Error: IP address required for 'ip' action\n";
                echo "Usage: php unrestrict.php ip 192.168.1.100\n";
                exit(1);
            }
            if (!filter_var($ipAddress, FILTER_VALIDATE_IP)) {
                echo "❌ Error: Invalid IP address format\n";
                exit(1);
            }
            unrestrict_by_ip($redis, $ipAddress);
            break;
            
        default:
            show_help();
            break;
    }
    
    $redis->close();
    
} catch (Exception $e) {
    echo "❌ Redis connection failed: " . $e->getMessage() . "\n";
    echo "Make sure Redis is running and accessible on 127.0.0.1:6379\n";
    exit(1);
}

/**
 * Remove all rate limits and locks
 */
function unrestrict_all($redis)
{
    echo "🧹 Removing ALL rate limits and locks...\n\n";
    
    // Get all rate limit related keys (including Laravel cache prefixes)
    $patterns = [
        'rate_*',                                    // Progressive rate limits
        'auth_*',                                    // Authentication rate limits
        'fortify_*',                                 // Fortify rate limits
        'laravel_cache:*',                          // Laravel cache rate limits
        'laravel-database-laravel-cache-rate_*',    // Laravel cache with database prefix
        'laravel-database-laravel-cache-auth_*',    // Laravel cache auth with database prefix
        'laravel-database-laravel-cache-fortify_*', // Laravel cache fortify with database prefix
        '*throttle*',                               // Any throttle keys
        '*limit*'                                   // Any limit keys
    ];
    
    $totalRemoved = 0;
    $categoryCounts = [];
    
    foreach ($patterns as $pattern) {
        $keys = $redis->keys($pattern);
        
        if (!empty($keys)) {
            foreach ($keys as $key) {
                // Categorize the key for reporting
                $category = get_key_category($key);
                $categoryCounts[$category] = ($categoryCounts[$category] ?? 0) + 1;
                
                $redis->del($key);
                $totalRemoved++;
            }
        }
    }
    
    echo "📊 Removal Summary:\n";
    foreach ($categoryCounts as $category => $count) {
        echo "   {$category}: {$count} keys\n";
    }
    echo "   Total: {$totalRemoved} keys removed\n\n";
    
    if ($totalRemoved > 0) {
        echo "✅ Successfully removed all rate limits and locks!\n";
        echo "🔓 All users and IPs are now unrestricted\n";
    } else {
        echo "ℹ️  No rate limits or locks found to remove\n";
    }
}

/**
 * Remove rate limits for specific IP
 */
function unrestrict_by_ip($redis, $ip)
{
    echo "🎯 Removing rate limits for IP: {$ip}\n\n";
    
    $removedKeys = [];
    
    // Define all possible key patterns for the IP (including Laravel cache prefixes)
    $keyPatterns = [
        // Progressive rate limiting
        "rate_limit_ip_{$ip}",
        "rate_violations_ip_{$ip}",
        "rate_lock_ip_{$ip}",
        "rate_global_timeout_ip_{$ip}",
        
        // Authentication rate limiting
        "auth_rate_limit_ip_{$ip}",
        "auth_violations_ip_{$ip}",
        "auth_lock_ip_{$ip}",
        "fortify_auth_rate_limit_ip_{$ip}",
        "fortify_auth_violations_ip_{$ip}",
        
        // Laravel cache prefixed keys
        "laravel-database-laravel-cache-auth_violations_ip_{$ip}",
        "laravel-database-laravel-cache-auth_lock_ip_{$ip}",
        "laravel-database-laravel-cache-rate_violations_ip_{$ip}",
        "laravel-database-laravel-cache-rate_lock_ip_{$ip}",
        "laravel-database-laravel-cache-rate_global_timeout_ip_{$ip}",
        
        // Wildcard patterns
        "rate_limit_ip_{$ip}_*",
        "*_ip_{$ip}",
        "*{$ip}*",
        "laravel-database-laravel-cache-*{$ip}*"
    ];
    
    foreach ($keyPatterns as $pattern) {
        $keys = $redis->keys($pattern);
        
        foreach ($keys as $key) {
            if ($redis->exists($key)) {
                $value = $redis->get($key);
                $ttl = $redis->ttl($key);
                
                $removedKeys[] = [
                    'key' => $key,
                    'value' => $value,
                    'ttl' => $ttl
                ];
                
                $redis->del($key);
            }
        }
    }
    
    if (!empty($removedKeys)) {
        echo "📋 Removed Keys for IP {$ip}:\n";
        foreach ($removedKeys as $keyInfo) {
            $keyType = get_key_type($keyInfo['key']);
            $ttlInfo = $keyInfo['ttl'] > 0 ? "TTL: {$keyInfo['ttl']}s" : "No expiry";
            echo "   🗑️  {$keyType}: {$keyInfo['key']} ({$ttlInfo})\n";
        }
        
        echo "\n✅ Successfully removed " . count($removedKeys) . " rate limit entries for IP {$ip}\n";
        echo "🔓 IP {$ip} is now unrestricted\n";
    } else {
        echo "ℹ️  No rate limits found for IP {$ip}\n";
        echo "   This IP was not restricted or locks have already expired\n";
    }
    
    // Show remaining restrictions (if any)
    show_remaining_restrictions($redis, $ip);
}

/**
 * Get key category for reporting
 */
function get_key_category($key)
{
    if (str_contains($key, 'auth_')) return 'Authentication Limits';
    if (str_contains($key, 'fortify_')) return 'Fortify Limits';
    if (str_contains($key, 'rate_lock_')) return 'Account Locks';
    if (str_contains($key, 'rate_violations_')) return 'Violation Counters';
    if (str_contains($key, 'rate_global_timeout_')) return 'Global Timeouts';
    if (str_contains($key, 'rate_limit_')) return 'Rate Limit Counters';
    return 'Other Cache Keys';
}

/**
 * Get key type for detailed reporting
 */
function get_key_type($key)
{
    if (str_contains($key, 'lock_')) return 'LOCK';
    if (str_contains($key, 'violations_')) return 'VIOLATIONS';
    if (str_contains($key, 'global_timeout_')) return 'TIMEOUT';
    if (str_contains($key, 'rate_limit_')) return 'RATE_LIMIT';
    if (str_contains($key, 'auth_')) return 'AUTH';
    return 'CACHE';
}

/**
 * Show remaining restrictions for the IP
 */
function show_remaining_restrictions($redis, $ip)
{
    echo "\n🔍 Checking for remaining restrictions...\n";
    
    $allKeys = $redis->keys("*{$ip}*");
    $remainingKeys = [];
    
    foreach ($allKeys as $key) {
        if ($redis->exists($key)) {
            $remainingKeys[] = $key;
        }
    }
    
    if (!empty($remainingKeys)) {
        echo "⚠️  Found " . count($remainingKeys) . " remaining keys containing IP {$ip}:\n";
        foreach ($remainingKeys as $key) {
            $ttl = $redis->ttl($key);
            $ttlInfo = $ttl > 0 ? " (expires in {$ttl}s)" : " (no expiry)";
            echo "   - {$key}{$ttlInfo}\n";
        }
    } else {
        echo "✅ No remaining restrictions found for IP {$ip}\n";
    }
}

/**
 * Show help information
 */
function show_help()
{
    echo "📚 Rate Limit Management Script Help\n\n";
    echo "USAGE:\n";
    echo "  php unrestrict.php [action] [options]\n\n";
    
    echo "ACTIONS:\n";
    echo "  all                     Remove ALL rate limits and locks\n";
    echo "  ip <ip_address>         Remove rate limits for specific IP\n";
    echo "  help                    Show this help message\n\n";
    
    echo "EXAMPLES:\n";
    echo "  php unrestrict.php all                    # Clear everything\n";
    echo "  php unrestrict.php ip 192.168.1.100      # Clear IP 192.168.1.100\n";
    echo "  php unrestrict.php ip 127.0.0.1          # Clear localhost\n\n";
    
    echo "WHAT GETS REMOVED:\n";
    echo "  ✓ Progressive rate limits (route-specific)\n";
    echo "  ✓ Global timeouts (application-wide blocks)\n";
    echo "  ✓ Violation counters (progressive escalation data)\n";
    echo "  ✓ Account locks (24-hour admin locks)\n";
    echo "  ✓ Authentication rate limits (login attempts)\n";
    echo "  ✓ Fortify rate limits (Laravel Fortify)\n";
    echo "  ✓ All associated cache entries\n\n";
    
    echo "SAFETY:\n";
    echo "  • This script only removes rate limiting data\n";
    echo "  • User data and application data remain untouched\n";
    echo "  • No permanent changes to database\n";
    echo "  • Rate limits will start fresh after removal\n\n";
    
    echo "REQUIREMENTS:\n";
    echo "  • Redis server running on 127.0.0.1:6379\n";
    echo "  • Redis database 1 accessible\n";
    echo "  • PHP with Redis extension\n";
}

?>