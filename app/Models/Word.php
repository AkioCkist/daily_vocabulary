<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Word extends Model
{
    use HasFactory;

    protected $fillable = [
        'word',
        'pronunciation',
        'definition',
        'example',
        'source',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_words')
                    ->withPivot('status', 'notes')
                    ->withTimestamps();
    }
}
