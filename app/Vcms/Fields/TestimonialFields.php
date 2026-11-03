<?php

namespace App\Vcms\Fields;

class TestimonialFields
{
    /**
     * @return list<array<string, mixed>>
     */
    public static function definition(): array
    {
        return [
            ['name' => 'name', 'type' => 'text', 'label' => 'Nama'],
            ['name' => 'role', 'type' => 'text', 'label' => 'Jabatan'],
            ['name' => 'content', 'type' => 'textarea', 'label' => 'Isi testimoni'],
            ['name' => 'rating', 'type' => 'select', 'label' => 'Rating', 'default' => 5, 'options' => [
                5 => '5 bintang', 4 => '4 bintang', 3 => '3 bintang', 2 => '2 bintang', 1 => '1 bintang',
            ]],
            ['name' => 'photo', 'type' => 'image', 'label' => 'Foto'],
            ['name' => 'logo', 'type' => 'image', 'label' => 'Logo perusahaan'],
            ['name' => 'is_active', 'type' => 'toggle', 'label' => 'Tampilkan di website'],
        ];
    }
}
