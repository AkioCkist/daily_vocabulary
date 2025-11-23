<?php

try {
    $redis = new Redis();
    $redis->connect('127.0.0.1', 6379);
    
    // Check all Redis databases (0-15)
    for ($db = 0; $db <= 15; $db++) {
        $redis->select($db);
        $keys = $redis->keys('*');
        if (!empty($keys)) {
            echo "=== Database $db ===\n";
            foreach ($keys as $key) {
                $ttl = $redis->ttl($key);
                echo "Key: $key | TTL: $ttl\n";
            }
            echo "\n";
        }
    }
    
    $redis->close();
    
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}