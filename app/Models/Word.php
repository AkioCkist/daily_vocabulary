<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


/**
 * Model representing a vocabulary word.
 *
 * @package App\Models
 */
class Word extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'word',
        'pronunciation',
        'definition',
        'example',
        'source',
        'topic',
        'cefr_level',
        'meaning',
    ];

    /**
     * Get the users associated with this word.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_words')
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
     * Get the user words (pivot records) for this word.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userWords()
    {
        return $this->hasMany(UserWord::class);
    }

    /**
     * Get the topic for this word.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function topic()
    {
        return $this->belongsTo(Topic::class, 'topic', 'name');
    }

    /**
     * Get the test attempts for this word.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function testAttempts()
    {
        return $this->hasMany(TestAttempt::class);
    }

    /**
     * Get the daily test items for this word.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function dailyTestItems()
    {
        return $this->hasMany(DailyTestItem::class);
    }

    /**
     * Scope to filter words by topic.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $topic
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByTopic($query, $topic)
    {
        return $query->where('topic', $topic);
    }

    /**
     * Scope to filter words by CEFR level.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $level
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByCefrLevel($query, $level)
    {
        return $query->where('cefr_level', $level);
    }

    /**
     * Scope to search words by meaning.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByMeaning($query, $search)
    {
        return $query->where('meaning', 'LIKE', '%' . $search . '%');
    }

    /**
     * Scope to search words by word text.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByWord($query, $search)
    {
        return $query->where('word', 'LIKE', '%' . $search . '%');
    }

    /**
     * Scope for dynamic filtering based on multiple criteria.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilter($query, array $filters)
    {
        return $query->when($filters['topic'] ?? null, function ($query, $topic) {
            return $query->byTopic($topic);
        })->when($filters['cefr_level'] ?? null, function ($query, $level) {
            return $query->byCefrLevel($level);
        })->when($filters['meaning_search'] ?? null, function ($query, $search) {
            return $query->byMeaning($search);
        })->when($filters['word_search'] ?? null, function ($query, $search) {
            return $query->byWord($search);
        });
    }

    /**
     * Get all unique topics.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getTopics()
    {
        return static::whereNotNull('topic')
                    ->distinct()
                    ->orderBy('topic')
                    ->pluck('topic');
    }

    /**
     * Get all CEFR levels.
     *
     * @return array
     */
    public static function getCefrLevels()
    {
        return ['A1', 'A2', 'B1', 'B2', 'C1', 'C2'];
    }
}
