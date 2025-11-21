<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Model representing an item (flashcard) within a saved study session.
 * 
 * Each item represents a flashcard that appeared in the original study session,
 * stored with its position to maintain the original order.
 *
 * @package App\Models
 */
class SavedSessionItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'saved_session_id',
        'flashcard_id',
        'position',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'position' => 'integer',
        'flashcard_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the saved session that owns this item.
     *
     * @return BelongsTo
     */
    public function savedSession(): BelongsTo
    {
        return $this->belongsTo(SavedSession::class);
    }

    /**
     * Get the flashcard data for this item.
     * Note: We store flashcard_id but don't create a direct relationship
     * since flashcards might be generated dynamically in your system.
     * You can add this relationship if you have a dedicated Flashcard model.
     */
    // public function flashcard(): BelongsTo
    // {
    //     return $this->belongsTo(Flashcard::class, 'flashcard_id');
    // }

    /**
     * Scope to get items ordered by position.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('position');
    }

    /**
     * Scope to get items for a specific saved session.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $sessionId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForSession($query, int $sessionId)
    {
        return $query->where('saved_session_id', $sessionId);
    }
}
