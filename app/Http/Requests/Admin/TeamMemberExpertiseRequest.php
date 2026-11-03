<?php

namespace App\Http\Requests\Admin;

class TeamMemberExpertiseRequest extends ListItemRequest
{
    protected function fields(): array
    {
        return [
            'team_member_id' => ['required', 'integer', 'exists:team_members,id'],
            'name' => ['required', 'string', 'max:150'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'team_member_id.*' => 'Anggota tim tidak ditemukan.',
            'name.required' => 'Keahlian wajib diisi.',
            'name.max' => 'Keahlian maksimal 150 karakter.',
        ];
    }
}
