<?php

namespace App\Vcms\Fields;

class AchievementFields
{
    /**
     * @return list<array<string, mixed>>
     */
    public static function definition(): array
    {
        return [
            ['name' => 'period', 'type' => 'text', 'label' => 'Tahun / periode', 'help' => 'Contoh: 2024 atau 2023 - 2024.'],
            ['name' => 'title', 'type' => 'text', 'label' => 'Nama pencapaian'],
            ['name' => 'description', 'type' => 'textarea', 'label' => 'Penjelasan'],
            ['name' => 'is_active', 'type' => 'toggle', 'label' => 'Tampilkan di website'],
        ];
    }
}
