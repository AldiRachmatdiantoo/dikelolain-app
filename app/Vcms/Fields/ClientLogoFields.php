<?php

namespace App\Vcms\Fields;

class ClientLogoFields
{
    /**
     * @return list<array<string, mixed>>
     */
    public static function definition(): array
    {
        return [
            ['name' => 'logo', 'type' => 'image', 'label' => 'Logo'],
            ['name' => 'name', 'type' => 'text', 'label' => 'Nama klien', 'help' => 'Dibaca mesin pencari dan pembaca layar.'],
            ['name' => 'is_active', 'type' => 'toggle', 'label' => 'Tampilkan di website'],
        ];
    }
}
