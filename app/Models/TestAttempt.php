<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Test attempt model for tracking user answers to test questions.
 *
 * @property int $id
 * @property int $user_id
 * @property int $word_id
 * @property int|null $daily_test_id
 * @property int|null $daily_test_item_id
 * @property bool $is_correct
 * @property string $answer_text
 * @property int|null $time_taken
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class TestAttempt extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'word_id',
        'daily_test_id',
        'daily_test_item_id',
        'is_correct',
        'answer_text',
        'time_taken',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_correct' => 'boolean',
    ];

    /**
     * Get the user that made this attempt.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the word for this attempt.
     */
    public function word(): BelongsTo
    {
        return $this->belongsTo(Word::class);
    }

    /**
     * Get the daily test for this attempt.
     */
    public function dailyTest(): BelongsTo
    {
        return $this->belongsTo(DailyTest::class);
    }

    /**
     * Get the daily test item for this attempt.
     */
    public function dailyTestItem(): BelongsTo
    {
        return $this->belongsTo(DailyTestItem::class);
    }

    /**
     * Scope to get correct attempts.
     */
    public function scopeCorrect($query)
    {
        return $query->where('is_correct', true);
    }

    /**
     * Scope to get incorrect attempts.
     */
    public function scopeIncorrect($query)
    {
        return $query->where('is_correct', false);
    }

    /**
     * Scope to get attempts for a specific user.
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get attempts for a specific word.
     */
    public function scopeForWord($query, int $wordId)
    {
        return $query->where('word_id', $wordId);
    }
}