<?php

namespace App\Vcms\Fields;

class TeamMemberFields
{
    /**
     * @return list<array<string, mixed>>
     */
    public static function definition(): array
    {
        return [
            ['name' => 'name', 'type' => 'text', 'label' => 'Nama', 'help' => 'Alamat halaman detail dibuat dari nama saat anggota ditambahkan dan tidak ikut berubah.'],
            ['name' => 'role', 'type' => 'text', 'label' => 'Jabatan'],
            ['name' => 'photo', 'type' => 'image', 'label' => 'Foto'],
            ['name' => 'experience', 'type' => 'text', 'label' => 'Pengalaman', 'help' => 'Contoh: 5 tahun.'],
            ['name' => 'about', 'type' => 'textarea', 'label' => 'Tentang (halaman detail)'],
            ['name' => 'expertise_intro', 'type' => 'textarea', 'label' => 'Pengantar keahlian (halaman detail)'],
            ['name' => 'skills_intro', 'type' => 'textarea', 'label' => 'Pengantar skill (halaman detail)'],
            ['name' => 'is_active', 'type' => 'toggle', 'label' => 'Tampilkan di website'],
        ];
    }
}
