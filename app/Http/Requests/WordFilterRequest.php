<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form request for word filtering operations.
 */
class WordFilterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'topic' => 'nullable|string|max:255',
            'cefr_level' => 'nullable|string|in:A1,A2,B1,B2,C1,C2',
            'meaning_search' => 'nullable|string|max:255',
            'word_search' => 'nullable|string|max:255',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100',
        ];
    }

    /**
     * Get the validated filters for word queries.
     *
     * @return array<string, mixed>
     */
    public function getFilters(): array
    {
        return $this->only([
            'topic',
            'cefr_level',
            'meaning_search',
            'word_search',
        ]);
    }

    /**
     * Get pagination parameters.
     *
     * @return array<string, int>
     */
    public function getPaginationParams(): array
    {
        return [
            'page' => $this->input('page', 1),
            'per_page' => $this->input('per_page', 20),
        ];
    }
}