<?php

namespace App\Vcms\Fields;

class CounterFields
{
    /**
     * @return list<array<string, mixed>>
     */
    public static function definition(): array
    {
        return [
            ['name' => 'number', 'type' => 'text', 'label' => 'Angka', 'help' => 'Contoh: 45, 1.000 atau 4,9.'],
            ['name' => 'suffix', 'type' => 'text', 'label' => 'Simbol di belakang angka', 'help' => 'Contoh: +, %, atau kosong.'],
            ['name' => 'label', 'type' => 'text', 'label' => 'Keterangan'],
            ['name' => 'is_active', 'type' => 'toggle', 'label' => 'Tampilkan di website'],
        ];
    }
}
