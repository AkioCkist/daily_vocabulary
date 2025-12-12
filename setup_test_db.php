<?php

require __DIR__ . '/vendor/autoload.php';

$connection = pg_connect("host=127.0.0.1 port=5432 user=postgres password='123qwe!@#'");

if (!$connection) {
    die("Failed to connect to PostgreSQL\n");
}

$result = pg_query($connection, "CREATE DATABASE daily_vocabulary_test;");

if ($result) {
    echo "Test database created successfully!\n";
} else {
    $error = pg_last_error($connection);
    if (strpos($error, 'already exists') !== false) {
        echo "Test database already exists\n";
    } else {
        echo "Error creating test database: " . $error . "\n";
    }
}

pg_close($connection);

// Now run migrations
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);

putenv('DB_DATABASE=daily_vocabulary_test');
putenv('APP_ENV=testing');

$status = $kernel->call('migrate', ['--env' => 'testing', '--force' => true]);

if ($status === 0) {
    echo "Migrations completed successfully!\n";
} else {
    echo "Migrations failed with status: $status\n";
}
