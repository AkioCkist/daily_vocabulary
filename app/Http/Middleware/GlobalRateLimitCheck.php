<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Carbon\Carbon;

class GlobalRateLimitCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip for certain routes that should always be accessible
        $exemptRoutes = [
            'logout',
            'admin.rate-limits.index',
            'admin.rate-limits.unlock-user',
            'admin.rate-limits.unlock-ip',
            'admin.rate-limits.unlock-auth-ip',
            'admin.rate-limits.unlock-auth-email',
        ];
        
        if ($request->route() && in_array($request->route()->getName(), $exemptRoutes)) {
            return $next($request);
        }
        
        $lockKey = $this->getLockKey($request);
        $violationKey = $this->getViolationKey($request);
        $globalTimeoutKey = $this->getGlobalTimeoutKey($request);
        
        // Check if user is permanently locked
        if (Cache::has($lockKey)) {
            $lockData = Cache::get($lockKey);
            $retryAfter = $this->calculateRemainingTime($lockData);
            throw new TooManyRequestsHttpException($retryAfter, 'Account temporarily locked. Contact administrator.');
        }
        
        // Check if user has an active global timeout
        if (Cache::has($globalTimeoutKey)) {
            $timeoutData = Cache::get($globalTimeoutKey);
            $retryAfter = $this->calculateRemainingTimeout($timeoutData);
            
            if ($retryAfter > 0) {
                throw new TooManyRequestsHttpException($retryAfter, 'You are currently under a rate limit timeout.');
            } else {
                // Timeout has expired, clear it
                Cache::forget($globalTimeoutKey);
            }
        }
        
        return $next($request);
    }
    
    /**
     * Calculate remaining lock time
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
    
    /**
     * Calculate remaining timeout
     */
    protected function calculateRemainingTimeout(array $timeoutData): int
    {
        if (!isset($timeoutData['expires_at'])) {
            return 0;
        }
        
        $expiresAt = Carbon::parse($timeoutData['expires_at']);
        return max(0, $expiresAt->diffInSeconds(now()));
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
}