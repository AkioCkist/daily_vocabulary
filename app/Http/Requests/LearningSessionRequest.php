<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form request for learning session operations.
 */
class LearningSessionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'cefr_level' => 'nullable|string|in:A1,A2,B1,B2,C1,C2',
            'topic' => 'nullable|string|max:50',
            'word_count' => 'nullable|integer|min:5|max:50',
            'session_type' => 'nullable|string|in:new,review,mixed',
            'new_words_ratio' => 'nullable|numeric|min:0|max:1',
            'review_words_ratio' => 'nullable|numeric|min:0|max:1',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $newRatio = $this->input('new_words_ratio', 0.7);
            $reviewRatio = $this->input('review_words_ratio', 0.3);
            
            if (abs($newRatio + $reviewRatio - 1.0) > 0.01) {
                $validator->errors()->add('ratios', 'The sum of new and review words ratios must equal 1.0');
            }
        });
    }

    /**
     * Get learning session configuration parameters.
     *
     * @return array<string, mixed>
     */
    public function getSessionConfig(): array
    {
        return [
            'cefr_level' => $this->input('cefr_level'),
            'topic' => $this->input('topic'),
            'word_count' => (int) $this->input('word_count', 10),
            'session_type' => $this->input('session_type', 'mixed'),
            'new_words_ratio' => $this->input('new_words_ratio', 0.7),
            'review_words_ratio' => $this->input('review_words_ratio', 0.3),
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'cefr_level.in' => 'Please select a valid CEFR level (A1, A2, B1, B2, C1, C2).',
            'topic.max' => 'Topic name must not exceed 50 characters.',
            'word_count.min' => 'Word count must be at least 5.',
            'word_count.max' => 'Word count must not exceed 50.',
            'session_type.in' => 'Session type must be one of: new, review, or mixed.',
        ];
    }
}