<?php

namespace App\Http\Requests\Admin;

use App\Models\PageListItem;
use App\Rules\MediaUpload;
use Illuminate\Validation\Rule;

class PageListItemRequest extends ListItemRequest
{
    protected function fields(): array
    {
        return [
            'list_key' => ['required', Rule::in(array_keys(PageListItem::LISTS))],
            'text' => ['required', 'string', 'max:200'],
            'icon' => ['nullable', new MediaUpload(images: true, videos: false)],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'list_key.*' => 'Daftar tidak dikenal.',
            'text.required' => 'Teks wajib diisi.',
            'text.max' => 'Teks maksimal 200 karakter.',
        ];
    }
}
