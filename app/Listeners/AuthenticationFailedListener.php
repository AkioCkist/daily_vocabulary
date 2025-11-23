<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Failed;
use Illuminate\Support\Facades\Cache;
use Laravel\Fortify\Fortify;

class AuthenticationFailedListener
{
    /**
     * Handle the event.
     */
    public function handle(Failed $event): void
    {
        $request = request();
        $ip = $request->ip();
        $email = strtolower($event->credentials['email'] ?? '');
        
        if (empty($email)) {
            return; // No email to track
        }
        
        $ipViolationKey = 'auth_violations_ip_' . $ip;
        $emailViolationKey = 'auth_violations_email_' . hash('sha256', $email);
        
        // Increment violation counts
        $ipViolations = Cache::get($ipViolationKey, 0) + 1;
        $emailViolations = Cache::get($emailViolationKey, 0) + 1;
        
        // Store violations for 7 days
        Cache::put($ipViolationKey, $ipViolations, now()->addDays(7));
        Cache::put($emailViolationKey, $emailViolations, now()->addDays(7));
        
        // Check if we should lock the IP or email
        $this->checkForLocks($ip, $email, $ipViolations, $emailViolations);
    }
    
    /**
     * Check if IP or email should be locked due to excessive violations
     */
    protected function checkForLocks(string $ip, string $email, int $ipViolations, int $emailViolations): void
    {
        $ipLockKey = 'auth_lock_ip_' . $ip;
        $emailLockKey = 'auth_lock_email_' . hash('sha256', $email);
        
        // Lock IP after 10 violations
        if ($ipViolations >= 10 && !Cache::has($ipLockKey)) {
            Cache::put($ipLockKey, [
                'locked_at' => now()->toISOString(),
                'retry_after' => 24 * 3600,
                'violations' => $ipViolations,
                'reason' => 'Excessive failed login attempts from IP'
            ], now()->addHours(24));
        }
        
        // Lock email after 10 violations
        if ($emailViolations >= 10 && !Cache::has($emailLockKey)) {
            Cache::put($emailLockKey, [
                'locked_at' => now()->toISOString(),
                'retry_after' => 24 * 3600,
                'violations' => $emailViolations,
                'reason' => 'Excessive failed login attempts for email'
            ], now()->addHours(24));
        }
    }
}