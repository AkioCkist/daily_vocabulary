<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

/**
 * Model representing a saved study session.
 * 
 * Users can save completed study sessions for later review.
 * Each saved session contains an ordered list of flashcards.
 *
 * @package App\Models
 */
class SavedSession extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'topic',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns this saved session.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all items (flashcards) in this saved session.
     *
     * @return HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(SavedSessionItem::class)->orderBy('position');
    }

    /**
     * Generate a unique slug for this session based on name.
     *
     * @param string $name
     * @param int $userId
     * @return string
     */
    public static function generateUniqueSlug(string $name, int $userId): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;
        // Truy vấn tất cả các slug của user một lần
        $existingSlugs = static::where('user_id', $userId)
            ->where('slug', 'LIKE', $baseSlug . '%')
            ->pluck('slug')->toArray();

        while (in_array($slug, $existingSlugs)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Auto-generate session name if not provided.
     *
     * @param string|null $topic
     * @return string
     */
    public static function generateSessionName(?string $topic = null): string
    {
        $date = now()->format('Y-m-d');
        return $topic ? "$date - $topic" : "Study Session - $date";
    }

    /**
     * Get flashcard IDs in order.
     *
     * @return array
     */
    public function getFlashcardIds(): array
    {
        return $this->items()->pluck('flashcard_id')->toArray();
    }

    /**
     * Get flashcard IDs shuffled.
     *
     * @return array
     */
    public function getShuffledFlashcardIds(): array
    {
        $ids = $this->getFlashcardIds();
        shuffle($ids);
        return $ids;
    }

    /**
     * Scope to get sessions for a specific user.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get sessions ordered by most recent first.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}
