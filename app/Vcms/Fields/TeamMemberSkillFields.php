<?php

namespace App\Vcms\Fields;

class TeamMemberSkillFields
{
    /**
     * @return list<array<string, mixed>>
     */
    public static function definition(): array
    {
        return [
            ['name' => 'team_member_id', 'type' => 'hidden', 'label' => ''],
            ['name' => 'name', 'type' => 'text', 'label' => 'Nama skill'],
            ['name' => 'percent', 'type' => 'number', 'label' => 'Persentase (0-100)'],
            ['name' => 'is_active', 'type' => 'toggle', 'label' => 'Tampilkan di website'],
        ];
    }
}
