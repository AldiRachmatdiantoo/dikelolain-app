<?php

namespace App\Http\Requests\Admin;

class ServiceBenefitRequest extends ListItemRequest
{
    protected function fields(): array
    {
        return [
            'service_id' => ['required', 'integer', 'exists:services,id'],
            'text' => ['required', 'string', 'max:200'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'service_id.*' => 'Layanan tidak ditemukan.',
            'text.required' => 'Manfaat wajib diisi.',
            'text.max' => 'Manfaat maksimal 200 karakter.',
        ];
    }
}
