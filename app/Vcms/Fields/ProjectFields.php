<?php

namespace App\Vcms\Fields;

use App\Models\Project;

class ProjectFields
{
    /**
     * @return list<array<string, mixed>>
     */
    public static function definition(): array
    {
        return [
            ['name' => 'title', 'type' => 'text', 'label' => 'Judul project'],
            ['name' => 'description', 'type' => 'textarea', 'label' => 'Deskripsi'],
            ['name' => 'media_type', 'type' => 'select', 'label' => 'Jenis', 'options' => Project::MEDIA_TYPES, 'default' => 'video'],
            ['name' => 'video_url', 'type' => 'url', 'label' => 'Link video YouTube', 'help' => 'Wajib untuk jenis Video.'],
            ['name' => 'image', 'type' => 'image', 'label' => 'Gambar', 'help' => 'Wajib untuk jenis Desain.'],
            ['name' => 'link', 'type' => 'url', 'label' => 'Link project (opsional)', 'help' => 'Kosong = tombol Visit Project tidak ditampilkan.'],
            ['name' => 'is_active', 'type' => 'toggle', 'label' => 'Tampilkan di website'],
        ];
    }
}
