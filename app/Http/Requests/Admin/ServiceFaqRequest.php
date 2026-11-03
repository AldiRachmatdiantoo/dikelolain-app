<?php

namespace App\Http\Requests\Admin;

class ServiceFaqRequest extends ListItemRequest
{
    protected function fields(): array
    {
        return [
            'service_id' => ['required', 'integer', 'exists:services,id'],
            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string', 'max:5000'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'service_id.*' => 'Layanan tidak ditemukan.',
            'question.required' => 'Pertanyaan wajib diisi.',
            'question.max' => 'Pertanyaan maksimal 255 karakter.',
            'answer.required' => 'Jawaban wajib diisi.',
            'answer.max' => 'Jawaban terlalu panjang.',
        ];
    }
}
