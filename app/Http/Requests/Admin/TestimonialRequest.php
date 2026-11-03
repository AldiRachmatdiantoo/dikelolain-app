<?php

namespace App\Http\Requests\Admin;

use App\Rules\MediaUpload;

class TestimonialRequest extends ListItemRequest
{
    protected function fields(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'role' => ['nullable', 'string', 'max:100'],
            'content' => ['required', 'string', 'max:2000'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'logo' => ['nullable', new MediaUpload(images: true, videos: false)],
            'photo' => ['nullable', new MediaUpload(images: true, videos: false)],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama wajib diisi.',
            'name.max' => 'Nama maksimal 100 karakter.',
            'role.max' => 'Jabatan maksimal 100 karakter.',
            'content.required' => 'Isi testimoni wajib diisi.',
            'content.max' => 'Isi testimoni terlalu panjang.',
            'rating.required' => 'Pilih rating.',
            'rating.between' => 'Rating harus 1 sampai 5.',
        ];
    }
}
