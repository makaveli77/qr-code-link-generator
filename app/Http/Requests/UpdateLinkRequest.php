<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLinkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Add authorization logic if needed
    }

    public function rules(): array
    {
        return [
            'original_url' => ['sometimes', 'url', 'max:2048'],
            'custom_alias' => ['nullable', 'string', 'min:3', 'max:20', 'alpha_dash', 'unique:links,short_code,' . $this->route('link')?->id],
            'expires_at' => ['nullable', 'date', 'after:now'],
            'password' => ['nullable', 'string', 'min:4', 'max:255'],
        ];
    }
}
