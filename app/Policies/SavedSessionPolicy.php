<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\SavedSession;
use App\Models\User;

/**
 * Policy for SavedSession authorization.
 * 
 * Ensures users can only access their own saved sessions.
 */
class SavedSessionPolicy
{
    /**
     * Determine whether the user can view any saved sessions.
     * 
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        // Any authenticated user can view their own saved sessions
        return true;
    }

    /**
     * Determine whether the user can view the saved session.
     * 
     * @param User $user
     * @param SavedSession $savedSession
     * @return bool
     */
    public function view(User $user, SavedSession $savedSession): bool
    {
        // User can only view their own saved sessions
        return $user->id === $savedSession->user_id;
    }

    /**
     * Determine whether the user can create saved sessions.
     * 
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        // Any authenticated user can create saved sessions
        return true;
    }

    /**
     * Determine whether the user can update the saved session.
     * 
     * @param User $user
     * @param SavedSession $savedSession
     * @return bool
     */
    public function update(User $user, SavedSession $savedSession): bool
    {
        // User can only update their own saved sessions
        return $user->id === $savedSession->user_id;
    }

    /**
     * Determine whether the user can delete the saved session.
     * 
     * @param User $user
     * @param SavedSession $savedSession
     * @return bool
     */
    public function delete(User $user, SavedSession $savedSession): bool
    {
        // User can only delete their own saved sessions
        return $user->id === $savedSession->user_id;
    }

    /**
     * Determine whether the user can review the saved session.
     * 
     * @param User $user
     * @param SavedSession $savedSession
     * @return bool
     */
    public function review(User $user, SavedSession $savedSession): bool
    {
        // User can only review their own saved sessions
        return $user->id === $savedSession->user_id;
    }

    /**
     * Determine whether the user can manage items in the saved session.
     * 
     * @param User $user
     * @param SavedSession $savedSession
     * @return bool
     */
    public function manageItems(User $user, SavedSession $savedSession): bool
    {
        // User can only manage items in their own saved sessions
        return $user->id === $savedSession->user_id;
    }

    /**
     * Determine whether the user can restore the saved session.
     * 
     * @param User $user
     * @param SavedSession $savedSession
     * @return bool
     */
    public function restore(User $user, SavedSession $savedSession): bool
    {
        // User can only restore their own saved sessions
        return $user->id === $savedSession->user_id;
    }

    /**
     * Determine whether the user can permanently delete the saved session.
     * 
     * @param User $user
     * @param SavedSession $savedSession
     * @return bool
     */
    public function forceDelete(User $user, SavedSession $savedSession): bool
    {
        // User can only force delete their own saved sessions
        return $user->id === $savedSession->user_id;
    }
}
