<?php

// Simple test page to trigger rate limits
echo "=== Rate Limit Test Page ===\n";
echo "Time: " . date('Y-m-d H:i:s') . "\n";
echo "This page should be rate limited.\n";
echo "Keep refreshing this page to trigger violations.\n";
echo "Then try visiting the homepage - you should get 429 errors on ALL pages.\n";

// Show current IP and user info if available
echo "IP Address: " . ($_SERVER['REMOTE_ADDR'] ?? 'Unknown') . "\n";
echo "User Agent: " . ($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown') . "\n";

?>