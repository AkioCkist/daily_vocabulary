<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form request for daily test operations.
 */
class DailyTestRequest extends FormRequest
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
            'question_count' => 'nullable|integer|min:5|max:50',
            'test_length' => 'nullable|integer|min:5|max:50',
            'new_words_ratio' => 'nullable|numeric|min:0|max:1',
            'review_words_ratio' => 'nullable|numeric|min:0|max:1',
            'unmastered_words_ratio' => 'nullable|numeric|min:0|max:1',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $newRatio = $this->input('new_words_ratio', 0.4);
            $reviewRatio = $this->input('review_words_ratio', 0.4);
            $unmasteredRatio = $this->input('unmastered_words_ratio', 0.2);
            
            if (abs($newRatio + $reviewRatio + $unmasteredRatio - 1.0) > 0.01) {
                $validator->errors()->add('ratios', 'The sum of all ratios must equal 1.0');
            }
        });
    }

    /**
     * Get test configuration parameters.
     *
     * @return array<string, mixed>
     */
    public function getTestConfig(): array
    {
        return [
            'cefr_level' => $this->input('cefr_level'),
            'topic' => $this->input('topic'),
            'question_count' => (int) $this->input('question_count', 10),
            'test_length' => (int) $this->input('question_count', 10), // Use question_count for test_length
            'new_words_ratio' => $this->input('new_words_ratio', 0.4),
            'review_words_ratio' => $this->input('review_words_ratio', 0.4),
            'unmastered_words_ratio' => $this->input('unmastered_words_ratio', 0.2),
        ];
    }
}