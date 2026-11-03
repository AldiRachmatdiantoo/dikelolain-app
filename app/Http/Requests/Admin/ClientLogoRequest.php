<?php

namespace App\Http\Requests\Admin;

use App\Rules\MediaUpload;

class ClientLogoRequest extends ListItemRequest
{
    protected function fields(): array
    {
        return [
            'logo' => ['required', new MediaUpload(images: true, videos: false)],
            'name' => ['nullable', 'string', 'max:100'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'logo.required' => 'Pilih file logo.',
            'name.max' => 'Nama maksimal 100 karakter.',
        ];
    }
}
