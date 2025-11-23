<?php
try {
    $redis = new Redis();
    $redis->connect('localhost', 6379);
    echo 'Redis connection: SUCCESS' . PHP_EOL;
    $redis->set('test_key', 'test_value');
    $value = $redis->get('test_key');
    echo 'Redis read/write test: ' . ($value === 'test_value' ? 'SUCCESS' : 'FAILED') . PHP_EOL;
    $redis->close();
} catch (Exception $e) {
    echo 'Redis connection: FAILED - ' . $e->getMessage() . PHP_EOL;
    echo 'Make sure Redis is running in WSL and accessible from Windows' . PHP_EOL;
}