<?php

namespace App\Http\Requests\Admin;

use App\Rules\MediaUpload;

class TeamMemberRequest extends ListItemRequest
{
    protected function fields(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'role' => ['required', 'string', 'max:100'],
            'photo' => ['required', new MediaUpload(images: true, videos: false)],
            'experience' => ['nullable', 'string', 'max:50'],
            'about' => ['nullable', 'string', 'max:3000'],
            'expertise_intro' => ['nullable', 'string', 'max:2000'],
            'skills_intro' => ['nullable', 'string', 'max:2000'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama wajib diisi.',
            'name.max' => 'Nama maksimal 100 karakter.',
            'role.required' => 'Jabatan wajib diisi.',
            'role.max' => 'Jabatan maksimal 100 karakter.',
            'photo.required' => 'Pilih foto.',
            'experience.max' => 'Pengalaman maksimal 50 karakter.',
            'about.max' => 'Teks terlalu panjang.',
            'expertise_intro.max' => 'Teks terlalu panjang.',
            'skills_intro.max' => 'Teks terlalu panjang.',
        ];
    }
}
