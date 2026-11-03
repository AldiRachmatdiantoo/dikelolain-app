<?php

namespace App\Http\Requests\Admin;

class TeamMemberSkillRequest extends ListItemRequest
{
    protected function fields(): array
    {
        return [
            'team_member_id' => ['required', 'integer', 'exists:team_members,id'],
            'name' => ['required', 'string', 'max:100'],
            'percent' => ['required', 'integer', 'between:0,100'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'team_member_id.*' => 'Anggota tim tidak ditemukan.',
            'name.required' => 'Nama skill wajib diisi.',
            'name.max' => 'Nama skill maksimal 100 karakter.',
            'percent.required' => 'Persentase wajib diisi.',
            'percent.integer' => 'Persentase harus angka bulat.',
            'percent.between' => 'Persentase antara 0 dan 100.',
        ];
    }
}
