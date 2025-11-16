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
    ];

    /**
     * Get the users associated with this word.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_words')
                    ->withPivot('status', 'notes')
                    ->withTimestamps();
    }
}
