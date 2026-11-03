<?php

namespace App\Vcms\Fields;

class PricingPlanFields
{
    /**
     * @return list<array<string, mixed>>
     */
    public static function definition(): array
    {
        return [
            ['name' => 'name', 'type' => 'text', 'label' => 'Nama paket'],
            ['name' => 'price', 'type' => 'text', 'label' => 'Harga', 'help' => 'Ditulis apa adanya, misalnya Rp 300 Rb.'],
            ['name' => 'period', 'type' => 'text', 'label' => 'Periode', 'help' => 'Misalnya /per month. Boleh kosong.'],
            ['name' => 'is_featured', 'type' => 'toggle', 'label' => 'Tonjolkan paket ini (kotak kuning)'],
            ['name' => 'button_text', 'type' => 'text', 'label' => 'Teks tombol', 'help' => 'Kosong = tombol tidak ditampilkan.'],
            ['name' => 'button_link', 'type' => 'url', 'label' => 'Link tombol'],
            ['name' => 'is_active', 'type' => 'toggle', 'label' => 'Tampilkan di website'],
        ];
    }
}
