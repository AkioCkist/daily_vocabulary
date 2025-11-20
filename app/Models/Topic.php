<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

/**
 * Topic model for categorizing vocabulary words.
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property bool $is_system
 * @property int|null $user_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class Topic extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'is_system',
        'user_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_system' => 'boolean',
    ];

    /**
     * Get the words for this topic.
     */
    public function words(): HasMany
    {
        return $this->hasMany(Word::class, 'topic', 'name');
    }

    /**
     * Get the user that owns this topic (for custom topics).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get only system topics.
     */
    public function scopeSystem(Builder $query): Builder
    {
        return $query->where('is_system', true);
    }

    /**
     * Scope to get only user topics.
     */
    public function scopeUserTopics(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId)->where('is_system', false);
    }

    /**
     * Scope to get available topics for a user (system + user's custom topics).
     */
    public function scopeAvailableForUser(Builder $query, int $userId): Builder
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('is_system', true)
              ->orWhere('user_id', $userId);
        });
    }

    /**
     * Check if this topic is a system topic.
     */
    public function isSystem(): bool
    {
        return $this->is_system;
    }

    /**
     * Check if this topic belongs to a specific user.
     */
    public function belongsToUser(int $userId): bool
    {
        return $this->user_id === $userId;
    }

    /**
     * Get the display type of the topic.
     */
    public function getTypeAttribute(): string
    {
        return $this->is_system ? 'system' : 'user';
    }
}