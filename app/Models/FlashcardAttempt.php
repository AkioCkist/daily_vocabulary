<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlashcardAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'word_id',
        'mode',
        'is_correct',
        'user_answer',
        'hints_used',
        'was_forgotten',
        'response_time_ms',
        'hint_progression',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'was_forgotten' => 'boolean',
        'hints_used' => 'integer',
        'response_time_ms' => 'integer',
        'hint_progression' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that made this flashcard attempt.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the word associated with this flashcard attempt.
     */
    public function word(): BelongsTo
    {
        return $this->belongsTo(Word::class);
    }

    /**
     * Scope to get attempts by flashcard type.
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('flashcard_type', $type);
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
     * Scope to get forgotten attempts.
     */
    public function scopeForgotten($query)
    {
        return $query->where('forgotten', true);
    }

    /**
     * Scope to get attempts with hints used.
     */
    public function scopeWithHints($query)
    {
        return $query->where('hints_used', '>', 0);
    }
}