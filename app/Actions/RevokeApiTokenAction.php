<?php

namespace App\Actions;

use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RevokeApiTokenAction
{
    /**
     * Revoke an API token for the user.
     *
     * @param User $user
     * @param int $tokenId
     * @return bool
     * @throws ModelNotFoundException
     */
    public function execute(User $user, int $tokenId): bool
    {
        $token = $user->tokens()
            ->where('id', $tokenId)
            ->firstOrFail();

        return $token->delete();
    }
}
