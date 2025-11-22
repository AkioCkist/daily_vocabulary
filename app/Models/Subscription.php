<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


/**
 * Model representing a user's subscription.
 *
 * @package App\Models
 */
class Subscription extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'user_id',
        'confirmed_at',
        'unsubscribed_at',
        'receive_ads',
        'incorrect_words_frequency',
        'topic_summary_frequency',
        'last_ads_sent_at',
        'last_incorrect_words_sent_at',
        'last_topic_summary_sent_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'receive_ads' => 'boolean',
        'last_ads_sent_at' => 'datetime',
        'last_incorrect_words_sent_at' => 'datetime',
        'last_topic_summary_sent_at' => 'datetime',
    ];

    /**
     * Get the user that owns this subscription.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
