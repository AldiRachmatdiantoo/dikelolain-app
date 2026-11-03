<?php

namespace App\Vcms\Fields;

class ToolFields
{
    /**
     * @return list<array<string, mixed>>
     */
    public static function definition(): array
    {
        return [
            ['name' => 'name', 'type' => 'text', 'label' => 'Nama tool'],
            ['name' => 'category', 'type' => 'text', 'label' => 'Kategori'],
            ['name' => 'icon', 'type' => 'image', 'label' => 'Ikon'],
            ['name' => 'is_active', 'type' => 'toggle', 'label' => 'Tampilkan di website'],
        ];
    }
}
