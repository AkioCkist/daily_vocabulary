<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class ProgressiveRateLimiter
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $maxAttempts = '5', string $decayMinutes = '1'): Response
    {
        $key = $this->resolveRequestSignature($request);
        $violationKey = $this->getViolationKey($request);
        $lockKey = $this->getLockKey($request);
        $globalTimeoutKey = $this->getGlobalTimeoutKey($request);
        
        // Check if user is permanently locked
        if (Cache::has($lockKey)) {
            $lockData = Cache::get($lockKey);
            throw new TooManyRequestsHttpException($lockData['retry_after'] ?? 3600, 'Account temporarily locked. Contact administrator.');
        }
        
        // Check if there's an active global timeout
        if (Cache::has($globalTimeoutKey)) {
            $globalTimeout = Cache::get($globalTimeoutKey);
            $remaining = $this->calculateRemainingTimeout($globalTimeout);
            if ($remaining > 0) {
                throw new TooManyRequestsHttpException($remaining);
            } else {
                // Global timeout expired, clear it
                Cache::forget($globalTimeoutKey);
            }
        }
        
        // Get current violation count
        $violations = Cache::get($violationKey, 0);
        $maxAttemptsInt = (int) $maxAttempts;
        
        // Check our own rate limiting using a custom cache key
        $customRateLimitKey = $key . '_custom_hits';
        $hits = Cache::get($customRateLimitKey, 0);
        
        // If user has exceeded attempts, this is a violation
        if ($hits >= $maxAttemptsInt) {
            // This is a new violation - increment violation count
            $violations++;
            Cache::put($violationKey, $violations, now()->addDays(7)); // Keep violation history for 7 days
            
            // Calculate progressive timeout based on violations
            $timeoutMinutes = $this->calculateTimeout($violations);
            $newRetryAfter = $timeoutMinutes * 60;
            
            // Set global timeout that applies to all routes
            Cache::put($globalTimeoutKey, [
                'violations' => $violations,
                'expires_at' => now()->addSeconds($newRetryAfter)->toISOString(),
                'timeout_duration' => $newRetryAfter
            ], now()->addSeconds($newRetryAfter));
            
            // If user has too many violations, lock them
            if ($violations >= 10) {
                $lockUntil = now()->addHours(24);
                Cache::put($lockKey, [
                    'locked_at' => now(),
                    'retry_after' => 24 * 3600,
                    'violations' => $violations
                ], $lockUntil);
                
                // Clear global timeout as user is now locked
                Cache::forget($globalTimeoutKey);
                
                throw new TooManyRequestsHttpException(24 * 3600, 'Account locked due to excessive violations. Contact administrator.');
            }
            
            // Clear the custom rate limit counter and start fresh
            Cache::forget($customRateLimitKey);
            
            throw new TooManyRequestsHttpException($newRetryAfter);
        }
        
        // Track the hit in our custom counter
        $decaySeconds = max(60, (int) $decayMinutes * 60); // Minimum 1 minute window
        Cache::put($customRateLimitKey, $hits + 1, now()->addSeconds($decaySeconds));
        
        return $next($request);
    }
    
    /**
     * Calculate progressive timeout based on violation count
     */
    protected function calculateTimeout(int $violations): int
    {
        $timeouts = [
            0 => 1,    // First timeout: 1 minute
            1 => 2,    // Second: 2 minutes
            2 => 5,    // Third: 5 minutes
            3 => 10,   // Fourth: 10 minutes
            4 => 15,   // Fifth: 15 minutes
            5 => 30,   // Sixth: 30 minutes
            6 => 60,   // Seventh: 1 hour
            7 => 120,  // Eighth: 2 hours
            8 => 240,  // Ninth: 4 hours
            9 => 480,  // Tenth: 8 hours
        ];
        
        return $timeouts[$violations] ?? 480; // Max 8 hours before permanent lock
    }
    
    /**
     * Resolve the rate limiting signature for the request
     */
    protected function resolveRequestSignature(Request $request): string
    {
        $user = $request->user();
        
        if ($user) {
            return 'rate_limit_user_' . $user->id . '_' . $request->route()->getName();
        }
        
        return 'rate_limit_ip_' . $request->ip() . '_' . $request->route()->getName();
    }
    
    /**
     * Get violation tracking key
     */
    protected function getViolationKey(Request $request): string
    {
        $user = $request->user();
        
        if ($user) {
            return 'rate_violations_user_' . $user->id;
        }
        
        return 'rate_violations_ip_' . $request->ip();
    }
    
    /**
     * Get lock key
     */
    protected function getLockKey(Request $request): string
    {
        $user = $request->user();
        
        if ($user) {
            return 'rate_lock_user_' . $user->id;
        }
        
        return 'rate_lock_ip_' . $request->ip();
    }
    
    /**
     * Get global timeout key
     */
    protected function getGlobalTimeoutKey(Request $request): string
    {
        $user = $request->user();
        
        if ($user) {
            return 'rate_global_timeout_user_' . $user->id;
        }
        
        return 'rate_global_timeout_ip_' . $request->ip();
    }
    
    /**
     * Calculate remaining timeout from global timeout data
     */
    protected function calculateRemainingTimeout(array $timeoutData): int
    {
        if (!isset($timeoutData['expires_at'])) {
            return 0;
        }
        
        $expiresAt = Carbon::parse($timeoutData['expires_at']);
        return max(0, $expiresAt->diffInSeconds(now()));
    }
}