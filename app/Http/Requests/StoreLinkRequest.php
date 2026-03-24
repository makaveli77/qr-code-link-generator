<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLinkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Auth logic here if needed
    }

    public function rules(): array
    {
        return [
            'original_url' => ['required', 'url', 'max:2048'],
            'custom_alias' => ['nullable', 'string', 'min:3', 'max:20', 'alpha_dash', 'unique:links,short_code,NULL,id,deleted_at,NULL'],
            'expires_at' => ['nullable', 'date', 'after:now'],
            'color' => ['nullable', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'background_color' => ['nullable', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'size' => ['nullable', 'integer', 'min:100', 'max:1000'],
            'logo' => ['nullable', 'image', 'max:2048'], // 2MB Max
            'password' => ['nullable', 'string', 'min:4', 'max:255'],
        ];
    }
}
