<?php

namespace App\Vcms\Fields;

class ServiceFields
{
    /**
     * @return list<array<string, mixed>>
     */
    public static function definition(): array
    {
        return [
            ['name' => 'name', 'type' => 'text', 'label' => 'Nama layanan', 'help' => 'Alamat halaman detail dibuat dari nama saat layanan ditambahkan dan tidak ikut berubah.'],
            ['name' => 'summary', 'type' => 'textarea', 'label' => 'Ringkasan (kartu dan tab)'],
            ['name' => 'image', 'type' => 'image', 'label' => 'Gambar kartu'],
            ['name' => 'icon', 'type' => 'image', 'label' => 'Ikon (tab di Home)', 'help' => 'Kosong = ikon bawaan.'],
            ['name' => 'feature_image', 'type' => 'image', 'label' => 'Gambar besar (halaman detail)', 'help' => 'Kosong = gambar kartu.'],
            ['name' => 'description', 'type' => 'textarea', 'label' => 'Deskripsi (halaman detail)', 'help' => 'Pisahkan paragraf dengan satu baris kosong.'],
            ['name' => 'benefits_title', 'type' => 'rich_text', 'label' => 'Judul manfaat (halaman detail)'],
            ['name' => 'benefits_intro', 'type' => 'textarea', 'label' => 'Pengantar manfaat (halaman detail)'],
            ['name' => 'is_active', 'type' => 'toggle', 'label' => 'Tampilkan di website'],
        ];
    }
}
