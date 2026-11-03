<?php

namespace App\Http\Requests\Admin;

class AchievementRequest extends ListItemRequest
{
    protected function fields(): array
    {
        return [
            'period' => ['required', 'string', 'max:50'],
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'period.required' => 'Tahun/periode wajib diisi.',
            'period.max' => 'Tahun/periode maksimal 50 karakter.',
            'title.required' => 'Nama pencapaian wajib diisi.',
            'title.max' => 'Nama maksimal 150 karakter.',
            'description.max' => 'Penjelasan terlalu panjang.',
        ];
    }
}
