<?php

namespace App\Services\Pages;

use App\Models\Faq;
use App\Services\FaqService;
use App\Services\SettingService;
use App\Vcms\Fields\FaqFields;

class FaqPageService
{
    public function __construct(
        private FaqService $faqs,
        private SettingService $settings,
    ) {
    }

    /**
     * @param  bool  $editing  also list hidden FAQs and empty categories, and add the editor field definitions
     */
    public function data(bool $editing = false): array
    {
        $categories = [];
        foreach ($this->faqs->grouped(activeOnly: !$editing) as $key => $faqs) {
            if ($faqs->isEmpty() && !$editing) {
                continue;
            }

            $categories[] = [
                'key' => $key,
                'anchor' => Faq::CATEGORIES[$key],
                'title' => $this->settings->get("faqs.category.{$key}"),
                'faqs' => $faqs,
            ];
        }

        return [
            'settings' => $this->settings,
            'categories' => $categories,
            'faqFields' => $editing ? FaqFields::definition() : [],
        ];
    }
}
