<?php

namespace App\Http\Requests\Admin;

use App\Rules\MediaUpload;

class ServiceRequest extends ListItemRequest
{
    protected function fields(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'summary' => ['required', 'string', 'max:500'],
            'image' => ['required', new MediaUpload(images: true, videos: false)],
            'icon' => ['nullable', new MediaUpload(images: true, videos: false)],
            'feature_image' => ['nullable', new MediaUpload(images: true, videos: false)],
            'description' => ['nullable', 'string', 'max:10000'],
            'benefits_title' => ['nullable', 'string', 'max:255'],
            'benefits_intro' => ['nullable', 'string', 'max:2000'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama layanan wajib diisi.',
            'name.max' => 'Nama maksimal 100 karakter.',
            'summary.required' => 'Ringkasan wajib diisi.',
            'summary.max' => 'Ringkasan maksimal 500 karakter.',
            'image.required' => 'Pilih gambar layanan.',
            'description.max' => 'Teks terlalu panjang.',
            'benefits_title.max' => 'Judul maksimal 255 karakter.',
            'benefits_intro.max' => 'Teks terlalu panjang.',
        ];
    }
}
