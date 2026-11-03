<?php

namespace App\Http\Requests\Admin;

class ProcessStepRequest extends ListItemRequest
{
    protected function fields(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string', 'max:2000'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul langkah wajib diisi.',
            'title.max' => 'Judul maksimal 150 karakter.',
            'description.required' => 'Penjelasan wajib diisi.',
            'description.max' => 'Penjelasan terlalu panjang.',
        ];
    }
}
