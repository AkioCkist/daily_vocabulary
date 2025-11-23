<?php

try {
    $redis = new Redis();
    $redis->connect('127.0.0.1', 6379);
    $redis->select(1);
    
    $keys = $redis->keys('rate_*');
    foreach($keys as $key) {
        $redis->del($key);
    }
    
    echo 'Cleared ' . count($keys) . " rate limiting keys\n";
    $redis->close();
} catch(Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}

?>