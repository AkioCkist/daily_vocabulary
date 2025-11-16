<?php


namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

/**
 * Action to update user profile information.
 *
 * @package App\Actions\Fortify
 */
class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Maximum length for name and email fields.
     */
    public const MAX_LENGTH = 255;

    /**
     * Validate and update the given user's profile information.
     *
     * @param User $user
     * @param array<string, string> $input
     * @return void
     */
    public function update(User $user, array $input): void
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:' . self::MAX_LENGTH],

            'email' => [
                'required',
                'string',
                'email',
                'max:' . self::MAX_LENGTH,
                Rule::unique('users')->ignore($user->id),
            ],
        ])->validateWithBag('updateProfileInformation');

        if ($input['email'] !== $user->email &&
            $user instanceof MustVerifyEmail) {
            $this->updateVerifiedUser($user, $input);
        } else {
            $user->forceFill([
                'name' => $input['name'],
                'email' => $input['email'],
            ])->save();
        }
    }

    /**
     * Update the given verified user's profile information.
     *
     * @param User $user
     * @param array<string, string> $input
     * @return void
     */
    protected function updateVerifiedUser(User $user, array $input): void
    {
        $user->forceFill([
            'name' => $input['name'],
            'email' => $input['email'],
            'email_verified_at' => null,
        ])->save();

        $user->sendEmailVerificationNotification();
    }
}
