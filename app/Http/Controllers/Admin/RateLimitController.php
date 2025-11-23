<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use Carbon\Carbon;

class RateLimitController extends Controller
{
    public function __construct()
    {
        // Authorization will be handled in routes
    }

    /**
     * Show locked users and violations
     */
    public function index()
    {
        $lockedUsers = $this->getLockedUsers();
        $userViolations = $this->getUserViolations();
        
        return response()->json([
            'locked_users' => $lockedUsers,
            'user_violations' => $userViolations,
        ]);
    }

    /**
     * Unlock a specific user
     */
    public function unlock(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
        ]);

        $userId = $request->user_id;
        $lockKey = 'rate_lock_user_' . $userId;
        $violationKey = 'rate_violations_user_' . $userId;
        $globalTimeoutKey = 'rate_global_timeout_user_' . $userId;

        // Remove lock and global timeout
        Cache::forget($lockKey);
        Cache::forget($globalTimeoutKey);
        
        // Optionally reset violations
        if ($request->reset_violations) {
            Cache::forget($violationKey);
        }

        return response()->json([
            'message' => 'User unlocked successfully',
            'user_id' => $userId,
        ]);
    }

    /**
     * Unlock by IP address
     */
    public function unlockIp(Request $request)
    {
        $request->validate([
            'ip_address' => 'required|ip',
        ]);

        $ip = $request->ip_address;
        $lockKey = 'rate_lock_ip_' . $ip;
        $violationKey = 'rate_violations_ip_' . $ip;
        $globalTimeoutKey = 'rate_global_timeout_ip_' . $ip;

        // Remove lock and global timeout
        Cache::forget($lockKey);
        Cache::forget($globalTimeoutKey);
        
        // Optionally reset violations
        if ($request->reset_violations) {
            Cache::forget($violationKey);
        }

        return response()->json([
            'message' => 'IP unlocked successfully',
            'ip_address' => $ip,
        ]);
    }
    
    /**
     * Unlock authentication locks by IP
     */
    public function unlockAuthIp(Request $request)
    {
        $request->validate([
            'ip_address' => 'required|ip',
        ]);

        $ip = $request->ip_address;
        $authLockKey = 'auth_lock_ip_' . $ip;
        $authViolationKey = 'auth_violations_ip_' . $ip;

        // Remove authentication lock
        Cache::forget($authLockKey);
        
        // Optionally reset authentication violations
        if ($request->reset_violations) {
            Cache::forget($authViolationKey);
        }

        return response()->json([
            'message' => 'Authentication IP lock removed successfully',
            'ip_address' => $ip,
        ]);
    }
    
    /**
     * Unlock authentication locks by email
     */
    public function unlockAuthEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $email = strtolower($request->email);
        $emailHash = hash('sha256', $email);
        $authLockKey = 'auth_lock_email_' . $emailHash;
        $authViolationKey = 'auth_violations_email_' . $emailHash;

        // Remove authentication lock
        Cache::forget($authLockKey);
        
        // Optionally reset authentication violations
        if ($request->reset_violations) {
            Cache::forget($authViolationKey);
        }

        return response()->json([
            'message' => 'Authentication email lock removed successfully',
            'email' => $email,
        ]);
    }

    /**
     * Get all locked users
     */
    protected function getLockedUsers(): array
    {
        $users = User::all();
        $lockedUsers = [];

        foreach ($users as $user) {
            $lockKey = 'rate_lock_user_' . $user->id;
            if (Cache::has($lockKey)) {
                $lockData = Cache::get($lockKey);
                $lockedUsers[] = [
                    'user' => $user,
                    'lock_data' => $lockData,
                    'remaining_time' => $this->calculateRemainingTime($lockData),
                ];
            }
        }

        return $lockedUsers;
    }

    /**
     * Get user violations
     */
    protected function getUserViolations(): array
    {
        $users = User::all();
        $violations = [];

        foreach ($users as $user) {
            $violationKey = 'rate_violations_user_' . $user->id;
            $violationCount = Cache::get($violationKey, 0);
            
            if ($violationCount > 0) {
                $violations[] = [
                    'user' => $user,
                    'violations' => $violationCount,
                ];
            }
        }

        return $violations;
    }

    /**
     * Calculate remaining lock time
     */
    protected function calculateRemainingTime(array $lockData): int
    {
        if (!isset($lockData['locked_at'])) {
            return 0;
        }

        $lockedAt = Carbon::parse($lockData['locked_at']);
        $retryAfter = $lockData['retry_after'] ?? 3600;
        $unlockTime = $lockedAt->addSeconds($retryAfter);

        return max(0, $unlockTime->diffInSeconds(now()));
    }
}