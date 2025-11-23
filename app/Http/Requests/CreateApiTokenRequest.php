<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateApiTokenRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'scopes' => ['array', 'nullable'],
            'scopes.*' => ['string', 'in:*,read,create,update,delete'],
            'expires_in_days' => ['integer', 'nullable', 'min:1', 'max:365'],
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
            'name.required' => 'Token name is required.',
            'name.max' => 'Token name must not exceed 255 characters.',
            'scopes.array' => 'Scopes must be an array.',
            'scopes.*.in' => 'Invalid scope. Allowed values: *, read, create, update, delete',
            'expires_in_days.integer' => 'Expiration days must be an integer.',
            'expires_in_days.min' => 'Expiration must be at least 1 day.',
            'expires_in_days.max' => 'Expiration cannot exceed 365 days.',
        ];
    }
}
