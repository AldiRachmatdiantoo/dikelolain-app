<?php

namespace App\Vcms\Fields;

class PricingFeatureFields
{
    /**
     * @return list<array<string, mixed>>
     */
    public static function definition(): array
    {
        return [
            ['name' => 'pricing_plan_id', 'type' => 'hidden', 'label' => ''],
            ['name' => 'text', 'type' => 'text', 'label' => 'Fitur'],
            ['name' => 'is_active', 'type' => 'toggle', 'label' => 'Tampilkan di website'],
        ];
    }
}
