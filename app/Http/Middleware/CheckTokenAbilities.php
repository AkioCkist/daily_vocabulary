<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTokenAbilities
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $abilities
     */
    public function handle(Request $request, Closure $next, string $abilities): Response
    {
        // Parse comma-separated abilities
        $requiredAbilities = array_map('trim', explode(',', $abilities));

        // Get the authenticated token
        $token = $request->user()?->currentAccessToken();

        // If there's no token (session auth), allow it
        if (!$token) {
            return $next($request);
        }

        // Get token abilities
        $tokenAbilities = $token->abilities;

        // If token has wildcard '*', it has all abilities
        if (in_array('*', $tokenAbilities, true)) {
            return $next($request);
        }

        // Check if token has at least one of the required abilities
        $hasRequiredAbility = array_intersect($requiredAbilities, $tokenAbilities);

        if (empty($hasRequiredAbility)) {
            return response()->json([
                'success' => false,
                'message' => 'This token does not have permission to perform this action.',
                'required_abilities' => $requiredAbilities,
                'token_abilities' => $tokenAbilities,
            ], 403);
        }

        return $next($request);
    }
}
