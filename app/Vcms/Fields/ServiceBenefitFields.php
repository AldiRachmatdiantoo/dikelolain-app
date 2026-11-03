<?php

namespace App\Vcms\Fields;

class ServiceBenefitFields
{
    /**
     * @return list<array<string, mixed>>
     */
    public static function definition(): array
    {
        return [
            ['name' => 'service_id', 'type' => 'hidden', 'label' => ''],
            ['name' => 'text', 'type' => 'text', 'label' => 'Manfaat'],
            ['name' => 'is_active', 'type' => 'toggle', 'label' => 'Tampilkan di website'],
        ];
    }
}
