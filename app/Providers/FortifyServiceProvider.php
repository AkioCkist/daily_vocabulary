<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Actions\Fortify\CustomRedirectIfTwoFactorAuthenticatable;
use App\Http\Responses\ConfirmPasswordViewResponse;
use App\Http\Responses\LoginViewResponse;
use App\Http\Responses\RegisterViewResponse;
use App\Http\Responses\RequestPasswordResetLinkViewResponse;
use App\Http\Responses\ResetPasswordViewResponse;
use App\Http\Responses\TwoFactorChallengeViewResponse;
use App\Http\Responses\VerifyEmailViewResponse;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Contracts\ConfirmPasswordViewResponse as ConfirmPasswordViewResponseContract;
use Laravel\Fortify\Contracts\LoginViewResponse as LoginViewResponseContract;
use Laravel\Fortify\Contracts\RegisterViewResponse as RegisterViewResponseContract;
use Laravel\Fortify\Contracts\RequestPasswordResetLinkViewResponse as RequestPasswordResetLinkViewResponseContract;
use Laravel\Fortify\Contracts\ResetPasswordViewResponse as ResetPasswordViewResponseContract;
use Laravel\Fortify\Contracts\TwoFactorChallengeViewResponse as TwoFactorChallengeViewResponseContract;
use Laravel\Fortify\Contracts\VerifyEmailViewResponse as VerifyEmailViewResponseContract;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(LoginViewResponseContract::class, LoginViewResponse::class);
        $this->app->singleton(RegisterViewResponseContract::class, RegisterViewResponse::class);
        $this->app->singleton(RequestPasswordResetLinkViewResponseContract::class, RequestPasswordResetLinkViewResponse::class);
        $this->app->singleton(ResetPasswordViewResponseContract::class, ResetPasswordViewResponse::class);
        $this->app->singleton(VerifyEmailViewResponseContract::class, VerifyEmailViewResponse::class);
        $this->app->singleton(ConfirmPasswordViewResponseContract::class, ConfirmPasswordViewResponse::class);
        $this->app->singleton(TwoFactorChallengeViewResponseContract::class, TwoFactorChallengeViewResponse::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::redirectUserForTwoFactorAuthenticationUsing(CustomRedirectIfTwoFactorAuthenticatable::class);

        RateLimiter::for('login', function (Request $request) {
            // Progressive rate limiting based on IP and email
            $ip = $request->ip();
            $email = strtolower($request->input(Fortify::username(), ''));
            
            // Track violations for both IP and email separately
            $ipViolationKey = 'auth_violations_ip_' . $ip;
            $emailViolationKey = 'auth_violations_email_' . hash('sha256', $email);
            
            $ipViolations = cache()->get($ipViolationKey, 0);
            $emailViolations = cache()->get($emailViolationKey, 0);
            
            // Use higher violation count for more restrictive limiting
            $maxViolations = max($ipViolations, $emailViolations);
            
            // Progressive limits and timeouts
            $baseAttempts = 5;
            $attempts = max(1, $baseAttempts - floor($maxViolations / 2));
            $timeoutMinutes = $this->calculateAuthTimeout($maxViolations);
            
            // Use combined key for rate limiting
            $throttleKey = 'auth_' . hash('sha256', $email . '|' . $ip);
            
            return Limit::perMinutes($timeoutMinutes, $attempts)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
    
    /**
     * Calculate progressive auth timeout based on violation count
     */
    protected function calculateAuthTimeout(int $violations): int
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
        
        return $timeouts[$violations] ?? 1440; // Max 24 hours
    }
}
