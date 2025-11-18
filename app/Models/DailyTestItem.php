<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Daily test item model for individual questions in a daily test.
 *
 * @property int $id
 * @property int $daily_test_id
 * @property int $word_id
 * @property string $question_type
 * @property array|null $options
 * @property string $correct_answer
 * @property array|null $result
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class DailyTestItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'daily_test_id',
        'word_id',
        'question_type',
        'options',
        'correct_answer',
        'result',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'options' => 'array',
        'result' => 'array',
    ];

    /**
     * Available question types.
     */
    public const QUESTION_TYPE_WORD_TO_DEFINITION = 'word_to_definition';
    public const QUESTION_TYPE_DEFINITION_TO_WORD = 'definition_to_word';

    /**
     * Get the daily test that owns this item.
     */
    public function dailyTest(): BelongsTo
    {
        return $this->belongsTo(DailyTest::class);
    }

    /**
     * Get the word for this test item.
     */
    public function word(): BelongsTo
    {
        return $this->belongsTo(Word::class);
    }

    /**
     * Get the test attempts for this item.
     */
    public function attempts(): HasMany
    {
        return $this->hasMany(TestAttempt::class);
    }

    /**
     * Check if this item has been answered.
     */
    public function isAnswered(): bool
    {
        return $this->attempts()->exists();
    }

    /**
     * Check if this item was answered correctly.
     */
    public function isCorrect(): bool
    {
        return $this->attempts()
            ->where('is_correct', true)
            ->exists();
    }

    /**
     * Get the latest attempt for this item.
     */
    public function latestAttempt(): ?TestAttempt
    {
        return $this->attempts()
            ->latest()
            ->first();
    }
}