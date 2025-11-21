<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Request validation for starting a review of a saved session.
 */
class ReviewSavedSessionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Authorization will be handled by policy
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'shuffle' => ['sometimes', 'boolean'],
            'flashcard_type' => ['sometimes', 'string', 'in:standard,reverse,both'],
        ];
    }

    /**
     * Get custom error messages for validation.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'shuffle.boolean' => 'Tham số shuffle phải là true/false.',
            'flashcard_type.in' => 'Loại flashcard phải là standard, reverse hoặc both.',
        ];
    }

    /**
     * Get the default values for the request.
     *
     * @return array
     */
    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);
        
        // Set defaults
        $validated['shuffle'] = $validated['shuffle'] ?? false;
        $validated['flashcard_type'] = $validated['flashcard_type'] ?? 'standard';
        
        return $key ? $validated[$key] ?? $default : $validated;
    }
}
