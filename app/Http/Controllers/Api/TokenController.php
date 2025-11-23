<?php

namespace App\Http\Controllers\Api;

use App\Actions\CreateApiTokenAction;
use App\Actions\RevokeApiTokenAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateApiTokenRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TokenController extends Controller
{
    /**
     * List all personal access tokens for the authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $tokens = $request->user()->tokens()
            ->select(['id', 'name', 'abilities', 'last_used_at', 'created_at', 'expires_at'])
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($token) {
                return [
                    'id' => $token->id,
                    'name' => $token->name,
                    'abilities' => $token->abilities,
                    'last_used_at' => $token->last_used_at?->diffForHumans(),
                    'last_used_at_date' => $token->last_used_at,
                    'created_at' => $token->created_at->format('Y-m-d H:i:s'),
                    'expires_at' => $token->expires_at?->format('Y-m-d H:i:s'),
                    'is_expired' => $token->expires_at && $token->expires_at->isPast(),
                ];
            });

        return response()->json([
            'data' => $tokens,
            'count' => $tokens->count(),
        ]);
    }

    /**
     * Create a new personal access token.
     */
    public function store(CreateApiTokenRequest $request): JsonResponse
    {
        try {
            $action = new CreateApiTokenAction();
            $result = $action->execute(
                $request->user(),
                $request->validated('name'),
                $request->validated('scopes') ?? ['*'],
                $request->validated('expires_in_days')
            );

            return response()->json([
                'success' => true,
                'message' => 'API token created successfully.',
                'token' => $result['plain_token'],
                'warning' => 'Save this token in a safe place. You will not be able to see it again!',
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    /**
     * Revoke (delete) a personal access token.
     */
    public function destroy(Request $request, int $token_id): JsonResponse
    {
        try {
            $action = new RevokeApiTokenAction();
            $action->execute($request->user(), $token_id);

            return response()->json([
                'success' => true,
                'message' => 'API token revoked successfully.',
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token not found.',
            ], 404);
        }
    }

    /**
     * Regenerate a personal access token (revoke old, create new).
     */
    public function regenerate(Request $request, int $token_id): JsonResponse
    {
        try {
            $oldToken = $request->user()->tokens()
                ->where('id', $token_id)
                ->firstOrFail();

            $name = $oldToken->name;
            $scopes = $oldToken->abilities;

            // Revoke old token
            $revokeAction = new RevokeApiTokenAction();
            $revokeAction->execute($request->user(), $token_id);

            // Create new token
            $createAction = new CreateApiTokenAction();
            $result = $createAction->execute(
                $request->user(),
                $name,
                $scopes,
                $oldToken->expires_at ? $oldToken->expires_at->diffInDays(now()) : null
            );

            return response()->json([
                'success' => true,
                'message' => 'API token regenerated successfully.',
                'token' => $result['plain_token'],
                'warning' => 'Save this token in a safe place. The old token has been revoked.',
            ], 201);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token not found.',
            ], 404);
        }
    }
}
