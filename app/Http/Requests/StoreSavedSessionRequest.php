<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\SavedSession;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

/**
 * Request validation for creating a new saved session.
 */
class StoreSavedSessionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
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
            'name' => ['required', 'string', 'max:255'],
            'topic' => ['nullable', 'string', 'max:255'],
            'flashcard_ids' => ['required', 'array', 'min:1'],
            'flashcard_ids.*' => ['required', 'integer', 'min:1'],
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
            'name.required' => 'Tên session là bắt buộc.',
            'name.max' => 'Tên session không được quá 255 ký tự.',
            'topic.max' => 'Topic không được quá 255 ký tự.',
            'flashcard_ids.required' => 'Cần ít nhất 1 flashcard để tạo session.',
            'flashcard_ids.array' => 'Danh sách flashcard không hợp lệ.',
            'flashcard_ids.min' => 'Cần ít nhất 1 flashcard để tạo session.',
            'flashcard_ids.*.integer' => 'ID flashcard phải là số nguyên.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Auto-generate name if not provided
        if (empty($this->name)) {
            $this->merge([
                'name' => SavedSession::generateSessionName($this->topic)
            ]);
        }
    }
}
