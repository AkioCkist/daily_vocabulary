<?php

// Simple test script to check API authentication responses
// Run this via: php scripts/test_api_auth.php

// Test unauthenticated API request
echo "Testing unauthenticated API request...\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/api/user');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Content-Type: application/json',
]);
curl_setopt($ch, CURLOPT_HEADER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status Code: $httpCode\n";
echo "Response:\n$response\n";
echo str_repeat('-', 50) . "\n";

// Test authenticated API request with invalid token
echo "Testing API request with invalid token...\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/api/words/filter');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Content-Type: application/json',
    'Authorization: Bearer invalid-token-here',
]);
curl_setopt($ch, CURLOPT_HEADER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status Code: $httpCode\n";
echo "Response:\n$response\n";