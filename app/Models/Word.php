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
     * Scope for searching by word.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByWord($query, $search)
    {
        return $query->where(function($q) use ($search) {
            // First priority: exact match
            $q->where('word', '=', $search)
              // Second priority: starts with search term
              ->orWhere('word', 'LIKE', $search . '%')
              // Third priority: contains search term
              ->orWhere('word', 'LIKE', '%' . $search . '%');
        })->orderByRaw("
            CASE 
                WHEN word = ? THEN 1 
                WHEN word LIKE ? THEN 2 
                ELSE 3 
            END, word ASC
        ", [$search, $search . '%']);
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
        return $query->when(!empty($filters['topic']), function ($query) use ($filters) {
            return $query->byTopic($filters['topic']);
        })->when(!empty($filters['cefr_level']), function ($query) use ($filters) {
            return $query->byCefrLevel($filters['cefr_level']);
        })->when(!empty($filters['meaning_search']), function ($query) use ($filters) {
            return $query->byMeaning($filters['meaning_search']);
        })->when(!empty($filters['word_search']), function ($query) use ($filters) {
            return $query->byWord($filters['word_search']);
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
