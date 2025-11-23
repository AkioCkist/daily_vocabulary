<?php

try {
    $redis = new Redis();
    $redis->connect('127.0.0.1', 6379);
    
    echo "=== All Redis Keys ===\n";
    $allKeys = $redis->keys('*');
    if (empty($allKeys)) {
        echo "No keys found in Redis\n";
    } else {
        foreach ($allKeys as $key) {
            $ttl = $redis->ttl($key);
            $value = $redis->get($key);
            echo "Key: $key | TTL: $ttl | Value: " . substr($value, 0, 100) . "\n";
        }
    }
    
    echo "\n=== Laravel Cache Keys ===\n";
    $laravelKeys = $redis->keys('*laravel*');
    if (empty($laravelKeys)) {
        echo "No Laravel cache keys found\n";
    } else {
        foreach ($laravelKeys as $key) {
            echo "Laravel Key: $key\n";
        }
    }
    
    $redis->close();
    
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}