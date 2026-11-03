<?php

namespace App\Vcms\Fields;

use App\Models\Faq;
use App\Services\SettingService;

class FaqFields
{
    /**
     * @return list<array<string, mixed>>
     */
    public static function definition(): array
    {
        $settings = app(SettingService::class);
        $categories = [];
        foreach (array_keys(Faq::CATEGORIES) as $category) {
            $categories[$category] = $settings->get("faqs.category.{$category}");
        }

        return [
            ['name' => 'category', 'type' => 'select', 'label' => 'Kategori', 'options' => $categories, 'default' => 'general'],
            ['name' => 'question', 'type' => 'text', 'label' => 'Pertanyaan'],
            ['name' => 'answer', 'type' => 'textarea', 'label' => 'Jawaban'],
            ['name' => 'is_active', 'type' => 'toggle', 'label' => 'Tampilkan di website'],
        ];
    }
}
