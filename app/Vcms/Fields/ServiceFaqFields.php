<?php

namespace App\Vcms\Fields;

class ServiceFaqFields
{
    /**
     * @return list<array<string, mixed>>
     */
    public static function definition(): array
    {
        return [
            ['name' => 'service_id', 'type' => 'hidden', 'label' => ''],
            ['name' => 'question', 'type' => 'text', 'label' => 'Pertanyaan'],
            ['name' => 'answer', 'type' => 'textarea', 'label' => 'Jawaban'],
            ['name' => 'is_active', 'type' => 'toggle', 'label' => 'Tampilkan di website'],
        ];
    }
}
