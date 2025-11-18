<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Daily test model for managing user's daily vocabulary tests.
 *
 * @property int $id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon $date
 * @property array $meta
 * @property bool $is_completed
 * @property int|null $score
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class DailyTest extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'date',
        'meta',
        'is_completed',
        'score',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date',
        'meta' => 'array',
        'is_completed' => 'boolean',
    ];

    /**
     * Get the user that owns this daily test.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the test items for this daily test.
     */
    public function items(): HasMany
    {
        return $this->hasMany(DailyTestItem::class);
    }

    /**
     * Get the test attempts for this daily test.
     */
    public function attempts(): HasMany
    {
        return $this->hasMany(TestAttempt::class);
    }

    /**
     * Scope to get tests for a specific date.
     */
    public function scopeForDate($query, \Carbon\Carbon $date)
    {
        return $query->whereDate('date', $date);
    }

    /**
     * Scope to get today's test for a user.
     */
    public function scopeTodayForUser($query, int $userId)
    {
        return $query->where('user_id', $userId)
                    ->whereDate('date', today());
    }

    /**
     * Calculate and update the test score.
     */
    public function calculateScore(): int
    {
        $totalItems = $this->items()->count();
        
        if ($totalItems === 0) {
            return 0;
        }

        $correctAnswers = $this->attempts()
            ->where('is_correct', true)
            ->count();

        $score = (int) round(($correctAnswers / $totalItems) * 100);
        
        $this->update(['score' => $score]);
        
        return $score;
    }

    /**
     * Mark the test as completed.
     */
    public function markCompleted(): void
    {
        $this->update([
            'is_completed' => true,
            'score' => $this->calculateScore(),
        ]);
    }
}