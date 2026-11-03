<?php

namespace App\Http\Requests\Admin;

class PricingPlanRequest extends ListItemRequest
{
    protected function fields(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'price' => ['required', 'string', 'max:50'],
            'period' => ['nullable', 'string', 'max:50'],
            'is_featured' => ['sometimes', 'boolean'],
            'button_text' => ['nullable', 'string', 'max:50'],
            'button_link' => ['nullable', 'string', 'max:2048', 'regex:#^(https?://|/|\#|mailto:|tel:)#i'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama paket wajib diisi.',
            'name.max' => 'Nama maksimal 100 karakter.',
            'price.required' => 'Harga wajib diisi.',
            'price.max' => 'Harga maksimal 50 karakter.',
            'period.max' => 'Periode maksimal 50 karakter.',
            'button_text.max' => 'Teks tombol maksimal 50 karakter.',
            'button_link.regex' => 'Link harus diawali http(s)://, /, #, mailto: atau tel:.',
            'button_link.max' => 'Link terlalu panjang.',
        ];
    }
}
