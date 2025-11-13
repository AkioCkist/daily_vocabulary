<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();
        
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'email_verified_at' => $user->email_verified_at,
                    'two_factor_confirmed_at' => $user->two_factor_confirmed_at,
                    'two_factor_secret' => $this->getTwoFactorSecret($user),
                    'two_factor_qr_code_svg' => $this->getTwoFactorQrCode($user),
                    'two_factor_recovery_codes' => $this->getTwoFactorRecoveryCodes($user),
                ] : null,
            ],
            'flash' => [
                'message' => fn () => $request->session()->get('message'),
                'status' => fn () => $request->session()->get('status'),
            ],
        ];
    }

    /**
     * Safely get the two-factor secret.
     */
    private function getTwoFactorSecret($user): ?string
    {
        if (!$user || !$user->two_factor_secret) {
            return null;
        }

        try {
            return decrypt($user->two_factor_secret);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Safely get the two-factor QR code.
     */
    private function getTwoFactorQrCode($user): ?string
    {
        if (!$user || !$user->two_factor_secret) {
            return null;
        }

        try {
            return $user->twoFactorQrCodeSvg();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Safely get the two-factor recovery codes.
     */
    private function getTwoFactorRecoveryCodes($user): ?array
    {
        if (!$user || !$user->two_factor_confirmed_at) {
            return null;
        }

        try {
            return $user->recoveryCodes();
        } catch (\Exception $e) {
            return null;
        }
    }
}
