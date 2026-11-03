<?php

namespace App\Http\Requests\Admin;

use App\Rules\MediaUpload;

class ToolRequest extends ListItemRequest
{
    protected function fields(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'max:100'],
            'icon' => ['required', new MediaUpload(images: true, videos: false)],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama tool wajib diisi.',
            'name.max' => 'Nama maksimal 100 karakter.',
            'category.max' => 'Kategori maksimal 100 karakter.',
            'icon.required' => 'Pilih file ikon.',
        ];
    }
}
