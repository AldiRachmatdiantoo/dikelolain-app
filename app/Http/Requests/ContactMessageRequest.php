<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * The Contact page form, sent by visitors (no login).
 */
class ContactMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:30', 'regex:/^\+?[0-9\s().-]{6,30}$/'],
            'message' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'Nama depan wajib diisi.',
            'first_name.max' => 'Nama depan maksimal 100 karakter.',
            'last_name.required' => 'Nama belakang wajib diisi.',
            'last_name.max' => 'Nama belakang maksimal 100 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email terlalu panjang.',
            'phone.required' => 'Nomor HP wajib diisi.',
            'phone.max' => 'Nomor HP terlalu panjang.',
            'phone.regex' => 'Nomor HP hanya boleh berisi angka, spasi, +, -, dan tanda kurung.',
            'message.max' => 'Pesan maksimal 5000 karakter.',
        ];
    }

    /**
     * Bots fill the hidden "website" field, people never see it.
     */
    public function isSpam(): bool
    {
        return $this->filled('website');
    }
}
