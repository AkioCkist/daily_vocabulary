<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DailyWordHistory extends Model
{
    use HasFactory;

    protected $table = 'daily_word_history';

    protected $fillable = ['word_id', 'date'];

    public function word()
    {
        return $this->belongsTo(Word::class);
    }
}

