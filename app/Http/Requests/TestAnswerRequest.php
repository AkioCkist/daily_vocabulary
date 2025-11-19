<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form request for test answer submissions.
 */
class TestAnswerRequest extends FormRequest
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
            'daily_test_item_id' => 'required|integer|exists:daily_test_items,id',
            'answer' => 'required|string|max:255',
            'question_type' => 'required|string|in:word_to_definition,definition_to_word',
            'time_taken' => 'nullable|integer|min:0',
        ];
    }

    /**
     * Get answer data for processing.
     *
     * @return array<string, mixed>
     */
    public function getAnswerData(): array
    {
        return [
            'daily_test_item_id' => $this->input('daily_test_item_id'),
            'answer' => trim($this->input('answer')),
            'question_type' => $this->input('question_type'),
            'time_taken' => $this->input('time_taken', 0),
        ];
    }
}