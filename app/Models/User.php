<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Fortify\TwoFactorAuthenticatable;


/**
 * Model representing an application user.
 *
 * @package App\Models
 */
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_recovery_codes' => 'json',
        ];
    }

    /**
     * Get the words associated with this user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function words()
    {
        return $this->belongsToMany(Word::class, 'user_words')
                    ->withPivot([
                        'is_learned',
                        'mastered',
                        'consecutive_correct',
                        'mistake_count',
                        'next_review_at',
                        'last_seen_at',
                        'status',
                        'notes'
                    ])
                    ->withTimestamps();
    }

    /**
     * Get the user's word progress records.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userWords()
    {
        return $this->hasMany(UserWord::class);
    }

    /**
     * Get the user's daily tests.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function dailyTests()
    {
        return $this->hasMany(DailyTest::class);
    }

    /**
     * Get the user's test attempts.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function testAttempts()
    {
        return $this->hasMany(TestAttempt::class);
    }

    /**
     * Get words that need review for this user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reviewWords()
    {
        return $this->userWords()
                    ->needsReview()
                    ->with('word');
    }

    /**
     * Get mastered words for this user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function masteredWords()
    {
        return $this->userWords()
                    ->mastered()
                    ->with('word');
    }

    /**
     * Get learned words for this user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function learnedWords()
    {
        return $this->userWords()
                    ->learned()
                    ->with('word');
    }

    /**
     * Get the user's custom topics.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function topics()
    {
        return $this->hasMany(Topic::class);
    }
}
