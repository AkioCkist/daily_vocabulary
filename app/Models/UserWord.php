<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model representing the relationship between a user and a word.
 *
 * @property int $id
 * @property int $user_id
 * @property int $word_id
 * @property bool $is_learned
 * @property bool $mastered
 * @property int $consecutive_correct
 * @property int $mistake_count
 * @property \Illuminate\Support\Carbon|null $next_review_at
 * @property \Illuminate\Support\Carbon|null $last_seen_at
 * @property string|null $status
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class UserWord extends Model
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
        'is_learned',
        'mastered',
        'consecutive_correct',
        'mistake_count',
        'next_review_at',
        'last_seen_at',
        'status',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_learned' => 'boolean',
        'mastered' => 'boolean',
        'next_review_at' => 'datetime',
        'last_seen_at' => 'datetime',
    ];

    /**
     * Get the user that owns this vocabulary entry.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the word associated with this entry.
     */
    public function word(): BelongsTo
    {
        return $this->belongsTo(Word::class);
    }

    /**
     * Scope to get words that need review.
     */
    public function scopeNeedsReview($query)
    {
        return $query->where('mistake_count', '>', 0)
            ->where('mastered', false);
    }

    /**
     * Scope to get mastered words.
     */
    public function scopeMastered($query)
    {
        return $query->where('mastered', true);
    }

    /**
     * Scope to get learned words.
     */
    public function scopeLearned($query)
    {
        return $query->where('is_learned', true);
    }

    /**
     * Scope to get unmastered words that have been seen.
     */
    public function scopeUnmasteredSeen($query)
    {
        return $query->where('is_learned', false)
            ->where('mastered', false)
            ->whereNotNull('last_seen_at');
    }

    /**
     * Mark word as learned.
     */
    public function markLearned(): void
    {
        $this->update([
            'is_learned' => true,
            'consecutive_correct' => $this->consecutive_correct + 1,
            'last_seen_at' => now(),
        ]);
    }

    /**
     * Handle correct answer.
     */
    public function handleCorrectAnswer(): void
    {
        $consecutiveCorrect = $this->consecutive_correct + 1;
        $mastered = $consecutiveCorrect >= 3; // Configurable threshold

        $this->update([
            'consecutive_correct' => $consecutiveCorrect,
            'last_seen_at' => now(),
            'mastered' => $mastered,
            'is_learned' => $mastered ? true : $this->is_learned,
        ]);

        // If mastered, we might want to remove from review
        if ($mastered) {
            $this->update(['mistake_count' => 0]);
        }
    }

    /**
     * Handle incorrect answer.
     */
    public function handleIncorrectAnswer(): void
    {
        $this->update([
            'consecutive_correct' => 0,
            'mistake_count' => $this->mistake_count + 1,
            'last_seen_at' => now(),
            'next_review_at' => now()->addHours(1), // Configurable delay
        ]);
    }

    /**
     * Add to review list.
     */
    public function addToReview(): void
    {
        $this->update([
            'mistake_count' => $this->mistake_count + 1,
            'next_review_at' => now()->addHours(1),
        ]);
    }
}
