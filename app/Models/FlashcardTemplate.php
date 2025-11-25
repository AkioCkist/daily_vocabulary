<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlashcardTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    /**
     * Get the user that owns the template.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Validate template settings structure.
     */
    public static function validateSettings(array $settings): bool
    {
        // Ensure required fields exist
        $requiredFields = ['word_count', 'flashcard_type'];

        foreach ($requiredFields as $field) {
            if (!isset($settings[$field])) {
                return false;
            }
        }

        return true;
    }
}
