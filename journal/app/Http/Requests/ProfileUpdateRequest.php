<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'full_name' => ['nullable', 'string', 'max:255'],
            'handle' => [
                'nullable',
                'string',
                'alpha_dash:ascii',
                'max:30',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'affiliation' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:180'],
            'avatar' => ['nullable', 'image', 'max:2048'],
            'remove_avatar' => ['boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'handle.alpha_dash' => 'Your handle may only contain letters, numbers, dashes and underscores.',
            'avatar.max' => 'Your portrait must be 2 MB or smaller.',
        ];
    }

    /**
     * People often type the @ themselves; store the handle without it.
     */
    protected function prepareForValidation(): void
    {
        if ($this->filled('handle')) {
            $this->merge(['handle' => ltrim(trim($this->input('handle')), '@')]);
        }
    }
}
