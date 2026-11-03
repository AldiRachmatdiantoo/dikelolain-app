<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Moves a list item one place up or down (POST /admin/api/{resource}/{id}/move).
 */
class MoveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'direction' => ['required', 'in:up,down'],
        ];
    }

    public function messages(): array
    {
        return [
            'direction.required' => 'Arah wajib diisi.',
            'direction.in' => 'Arah harus up atau down.',
        ];
    }
}
