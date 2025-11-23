<?php
// Simple test to check if rate limiting works with database cache
echo "Testing rate limiting with database cache...\n";
echo "1. Trigger a rate limited route (like login) in your browser\n";
echo "2. Check the cache table in PostgreSQL:\n";
echo "   SELECT key, value FROM cache WHERE key LIKE '%throttle%' OR key LIKE '%rate%';\n";
echo "3. You should see entries related to rate limiting\n";