<?php

namespace App\Http\Requests\Admin;

class CounterRequest extends ListItemRequest
{
    protected function fields(): array
    {
        return [
            // Digits with optional thousand/decimal separators (1,000 or 4.9); the counter animation reads them.
            'number' => ['required', 'string', 'max:12', 'regex:/^\d+([.,]\d+)*$/'],
            'suffix' => ['nullable', 'string', 'max:30'],
            'label' => ['required', 'string', 'max:100'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'number.required' => 'Angka wajib diisi.',
            'number.regex' => 'Isi angka saja, misalnya 45, 1.000 atau 4,9.',
            'number.max' => 'Angka maksimal 12 karakter.',
            'suffix.max' => 'Simbol maksimal 30 karakter.',
            'label.required' => 'Keterangan wajib diisi.',
            'label.max' => 'Keterangan maksimal 100 karakter.',
        ];
    }
}
