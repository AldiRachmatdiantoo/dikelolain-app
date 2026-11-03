<?php

namespace App\Vcms\Fields;

class TeamMemberExpertiseFields
{
    /**
     * @return list<array<string, mixed>>
     */
    public static function definition(): array
    {
        return [
            ['name' => 'team_member_id', 'type' => 'hidden', 'label' => ''],
            ['name' => 'name', 'type' => 'text', 'label' => 'Keahlian'],
            ['name' => 'is_active', 'type' => 'toggle', 'label' => 'Tampilkan di website'],
        ];
    }
}
