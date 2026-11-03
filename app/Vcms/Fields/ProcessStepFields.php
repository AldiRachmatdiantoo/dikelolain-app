<?php

namespace App\Vcms\Fields;

class ProcessStepFields
{
    /**
     * @return list<array<string, mixed>>
     */
    public static function definition(): array
    {
        return [
            ['name' => 'title', 'type' => 'text', 'label' => 'Judul langkah', 'help' => 'Nomor urut (01, 02, ...) ditambahkan otomatis.'],
            ['name' => 'description', 'type' => 'textarea', 'label' => 'Penjelasan'],
            ['name' => 'is_active', 'type' => 'toggle', 'label' => 'Tampilkan di website'],
        ];
    }
}
