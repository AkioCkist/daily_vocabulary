<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Carbon\Carbon;

class AuthRateLimiter
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $maxAttempts = '5', string $decayMinutes = '15'): Response
    {
        // For auth attempts, we track by IP, email, and combination
        $ip = $request->ip();
        $email = $request->input('email', '');
        
        $ipKey = 'auth_rate_limit_ip_' . $ip;
        $emailKey = 'auth_rate_limit_email_' . hash('sha256', strtolower($email));
        $combinedKey = 'auth_rate_limit_combined_' . hash('sha256', $ip . '_' . strtolower($email));
        
        $ipViolationKey = 'auth_violations_ip_' . $ip;
        $emailViolationKey = 'auth_violations_email_' . hash('sha256', strtolower($email));
        
        $ipLockKey = 'auth_lock_ip_' . $ip;
        $emailLockKey = 'auth_lock_email_' . hash('sha256', strtolower($email));
        
        // Check if IP is locked
        if (Cache::has($ipLockKey)) {
            $lockData = Cache::get($ipLockKey);
            $remaining = $this->calculateRemainingTime($lockData);
            throw new TooManyRequestsHttpException($remaining, 'IP temporarily locked due to excessive login attempts.');
        }
        
        // Check if email is locked
        if (Cache::has($emailLockKey)) {
            $lockData = Cache::get($emailLockKey);
            $remaining = $this->calculateRemainingTime($lockData);
            throw new TooManyRequestsHttpException($remaining, 'Email temporarily locked due to excessive login attempts.');
        }
        
        $maxAttemptsInt = (int) $maxAttempts;
        
        // Check rate limits for all tracking methods
        $ipHits = Cache::get($ipKey, 0);
        $emailHits = Cache::get($emailKey, 0);
        $combinedHits = Cache::get($combinedKey, 0);
        
        // If any of the limits are exceeded, this is a violation
        if ($ipHits >= $maxAttemptsInt || $emailHits >= $maxAttemptsInt || $combinedHits >= $maxAttemptsInt) {
            
            // Increment violation counts
            $ipViolations = Cache::get($ipViolationKey, 0) + 1;
            $emailViolations = Cache::get($emailViolationKey, 0) + 1;
            
            // Store violation counts
            Cache::put($ipViolationKey, $ipViolations, now()->addDays(7));
            Cache::put($emailViolationKey, $emailViolations, now()->addDays(7));
            
            // Calculate progressive timeouts
            $ipTimeout = $this->calculateTimeout($ipViolations);
            $emailTimeout = $this->calculateTimeout($emailViolations);
            
            // Use the longer timeout for security
            $timeout = max($ipTimeout, $emailTimeout);
            $timeoutSeconds = $timeout * 60;
            
            // Lock IP if too many violations
            if ($ipViolations >= 10) {
                Cache::put($ipLockKey, [
                    'locked_at' => now()->toISOString(),
                    'retry_after' => 24 * 3600,
                    'violations' => $ipViolations
                ], now()->addHours(24));
                throw new TooManyRequestsHttpException(24 * 3600, 'IP locked due to excessive login attempts. Contact administrator.');
            }
            
            // Lock email if too many violations
            if ($emailViolations >= 10) {
                Cache::put($emailLockKey, [
                    'locked_at' => now()->toISOString(),
                    'retry_after' => 24 * 3600,
                    'violations' => $emailViolations
                ], now()->addHours(24));
                throw new TooManyRequestsHttpException(24 * 3600, 'Email locked due to excessive login attempts. Contact administrator.');
            }
            
            // Clear old rate limit counters and set new ones with progressive timeout
            Cache::forget($ipKey);
            Cache::forget($emailKey);
            Cache::forget($combinedKey);
            
            throw new TooManyRequestsHttpException($timeoutSeconds, 'Too many login attempts. Progressive timeout applied.');
        }
        
        // Track the authentication attempt
        $decaySeconds = (int) $decayMinutes * 60;
        
        Cache::put($ipKey, $ipHits + 1, now()->addSeconds($decaySeconds));
        Cache::put($emailKey, $emailHits + 1, now()->addSeconds($decaySeconds));
        Cache::put($combinedKey, $combinedHits + 1, now()->addSeconds($decaySeconds));
        
        return $next($request);
    }
    
    /**
     * Calculate progressive timeout based on violation count
     */
    protected function calculateTimeout(int $violations): int
    {
        $timeouts = [
            0 => 5,    // First timeout: 5 minutes
            1 => 10,   // Second: 10 minutes
            2 => 15,   // Third: 15 minutes
            3 => 30,   // Fourth: 30 minutes
            4 => 60,   // Fifth: 1 hour
            5 => 120,  // Sixth: 2 hours
            6 => 240,  // Seventh: 4 hours
            7 => 480,  // Eighth: 8 hours
            8 => 720,  // Ninth: 12 hours
            9 => 1440, // Tenth: 24 hours
        ];
        
        return $timeouts[$violations] ?? 1440; // Max 24 hours before permanent lock
    }
    
    /**
     * Calculate remaining time from lock data
     */
    protected function calculateRemainingTime(array $lockData): int
    {
        if (!isset($lockData['locked_at'])) {
            return 24 * 3600; // Default 24 hours
        }

        $lockedAt = Carbon::parse($lockData['locked_at']);
        $retryAfter = $lockData['retry_after'] ?? 24 * 3600;
        $unlockTime = $lockedAt->addSeconds($retryAfter);

        return max(0, $unlockTime->diffInSeconds(now()));
    }
}