<?php

namespace App\Actions\Fortify;

use Illuminate\Auth\Events\Failed;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\RedirectsIfTwoFactorAuthenticatable;
use Laravel\Fortify\Events\TwoFactorAuthenticationChallenged;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\LoginRateLimiter;
use Laravel\Fortify\TwoFactorAuthenticatable;

class CustomRedirectIfTwoFactorAuthenticatable implements RedirectsIfTwoFactorAuthenticatable
{
    /**
     * The guard implementation.
     */
    protected $guard;

    /**
     * The login rate limiter instance.
     */
    protected $limiter;

    /**
     * Create a new controller instance.
     */
    public function __construct(StatefulGuard $guard, LoginRateLimiter $limiter)
    {
        $this->guard = $guard;
        $this->limiter = $limiter;
    }

    /**
     * Handle the incoming request.
     */
    public function handle($request, $next)
    {
        $user = $this->validateCredentials($request);

        Log::info('2FA Check', [
            'user_id' => $user?->id,
            'has_secret' => !empty($user?->two_factor_secret),
            'has_confirmed_at' => !empty($user?->two_factor_confirmed_at),
            'confirms_2fa' => Fortify::confirmsTwoFactorAuthentication(),
            'has_trait' => $user ? in_array(TwoFactorAuthenticatable::class, class_uses_recursive($user)) : false,
        ]);

        if (Fortify::confirmsTwoFactorAuthentication()) {
            if (optional($user)->two_factor_secret &&
                ! is_null(optional($user)->two_factor_confirmed_at) &&
                in_array(TwoFactorAuthenticatable::class, class_uses_recursive($user))) {
                Log::info('Redirecting to 2FA challenge');
                return $this->twoFactorChallengeResponse($request, $user);
            } else {
                Log::info('Proceeding without 2FA');
                return $next($request);
            }
        }

        if (optional($user)->two_factor_secret &&
            in_array(TwoFactorAuthenticatable::class, class_uses_recursive($user))) {
            Log::info('Redirecting to 2FA challenge (non-confirm mode)');
            return $this->twoFactorChallengeResponse($request, $user);
        }

        Log::info('No 2FA required');
        return $next($request);
    }

    /**
     * Attempt to validate the incoming credentials.
     */
    protected function validateCredentials($request)
    {
        if (Fortify::$authenticateUsingCallback) {
            return tap(call_user_func(Fortify::$authenticateUsingCallback, $request), function ($user) use ($request) {
                if (! $user) {
                    $this->fireFailedEvent($request);
                    $this->throwFailedAuthenticationException($request);
                }
            });
        }

        $provider = $this->guard->getProvider();

        return tap($provider->retrieveByCredentials($request->only(Fortify::username(), 'password')), function ($user) use ($provider, $request) {
            if (! $user || ! $provider->validateCredentials($user, ['password' => $request->password])) {
                $this->fireFailedEvent($request, $user);
                $this->throwFailedAuthenticationException($request);
            }

            if (config('hashing.rehash_on_login', true) && method_exists($provider, 'rehashPasswordIfRequired')) {
                $provider->rehashPasswordIfRequired($user, ['password' => $request->password]);
            }
        });
    }

    /**
     * Redirect the user to the two factor authentication challenge.
     */
    protected function twoFactorChallengeResponse($request, $user)
    {
        $request->session()->put([
            'login.id' => $user->getKey(),
            'login.remember' => $request->filled('remember'),
        ]);

        TwoFactorAuthenticationChallenged::dispatch($user);

        return app(\Laravel\Fortify\Contracts\TwoFactorChallengeViewResponse::class);
    }

    /**
     * Fire the failed authentication attempt event with the given arguments.
     */
    protected function fireFailedEvent($request, $user = null)
    {
        event(new Failed(config('fortify.guard'), $user, [
            Fortify::username() => $request->{Fortify::username()},
            'password' => $request->password,
        ]));
    }

    /**
     * Throw a failed authentication validation exception.
     */
    protected function throwFailedAuthenticationException($request)
    {
        $this->limiter->increment($request);

        throw ValidationException::withMessages([
            Fortify::username() => [trans('auth.failed')],
        ]);
    }
}