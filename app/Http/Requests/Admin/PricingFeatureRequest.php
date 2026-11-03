<?php

namespace App\Http\Requests\Admin;

class PricingFeatureRequest extends ListItemRequest
{
    protected function fields(): array
    {
        return [
            'pricing_plan_id' => ['required', 'integer', 'exists:pricing_plans,id'],
            'text' => ['required', 'string', 'max:200'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'pricing_plan_id.*' => 'Paket harga tidak ditemukan.',
            'text.required' => 'Fitur wajib diisi.',
            'text.max' => 'Fitur maksimal 200 karakter.',
        ];
    }
}
