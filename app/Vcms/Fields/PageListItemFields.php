<?php

namespace App\Vcms\Fields;

class PageListItemFields
{
    /**
     * @param  bool  $withIcon  the list shows an icon next to each line (Pricing benefits)
     * @return list<array<string, mixed>>
     */
    public static function definition(bool $withIcon = false): array
    {
        return array_values(array_filter([
            ['name' => 'list_key', 'type' => 'hidden', 'label' => ''],
            ['name' => 'text', 'type' => 'text', 'label' => 'Teks'],
            $withIcon ? ['name' => 'icon', 'type' => 'image', 'label' => 'Ikon', 'help' => 'Kosong = tanpa ikon.'] : null,
            ['name' => 'is_active', 'type' => 'toggle', 'label' => 'Tampilkan di website'],
        ]));
    }
}
