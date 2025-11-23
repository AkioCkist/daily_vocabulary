<?php

namespace App\Actions;

use App\Models\User;
use Illuminate\Validation\ValidationException;

class CreateApiTokenAction
{
    /**
     * Create a new API token for the user.
     *
     * @param User $user
     * @param string $name
     * @param array<string> $scopes
     * @param int|null $expiresInDays
     * @return array{token: string, plain_token: string}
     */
    public function execute(
        User $user,
        string $name,
        array $scopes = ['*'],
        ?int $expiresInDays = null
    ): array {
        // Validate token name uniqueness for this user
        $existingToken = $user->tokens()
            ->where('name', $name)
            ->first();

        if ($existingToken) {
            throw ValidationException::withMessages([
                'name' => 'A token with this name already exists.',
            ]);
        }

        // Create the token
        $token = $user->createToken(
            $name,
            $scopes,
            $expiresInDays ? now()->addDays($expiresInDays) : null
        );

        return [
            'token' => $token->accessToken,
            'plain_token' => $token->plainTextToken,
        ];
    }
}
