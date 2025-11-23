<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Carbon\Carbon;

class CheckAuthenticationLocks
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only apply to login attempts
        if ($request->routeIs('login') && $request->isMethod('POST')) {
            $ip = $request->ip();
            $email = strtolower($request->input('email', ''));
            
            if (!empty($email)) {
                $ipLockKey = 'auth_lock_ip_' . $ip;
                $emailLockKey = 'auth_lock_email_' . hash('sha256', $email);
                
                // Check if IP is locked
                if (Cache::has($ipLockKey)) {
                    $lockData = Cache::get($ipLockKey);
                    $remaining = $this->calculateRemainingTime($lockData);
                    if ($remaining > 0) {
                        throw new TooManyRequestsHttpException(
                            $remaining, 
                            'IP address temporarily locked due to excessive failed login attempts.'
                        );
                    } else {
                        // Lock expired, remove it
                        Cache::forget($ipLockKey);
                    }
                }
                
                // Check if email is locked
                if (Cache::has($emailLockKey)) {
                    $lockData = Cache::get($emailLockKey);
                    $remaining = $this->calculateRemainingTime($lockData);
                    if ($remaining > 0) {
                        throw new TooManyRequestsHttpException(
                            $remaining, 
                            'This email address is temporarily locked due to excessive failed login attempts.'
                        );
                    } else {
                        // Lock expired, remove it
                        Cache::forget($emailLockKey);
                    }
                }
            }
        }
        
        return $next($request);
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