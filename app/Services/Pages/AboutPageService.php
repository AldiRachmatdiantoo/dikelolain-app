<?php

namespace App\Services\Pages;

use App\Models\Section;
use App\Services\FaqService;
use App\Services\SettingService;

class AboutPageService
{
    public function __construct(
        private FaqService $faqs,
        private SettingService $settings,
    ) {
    }

    public function data(): array
    {
        return [
            // Old key-value content for the sections not migrated yet (Epic 3).
            'sections' => Section::with('contents.media')->active()->ordered()->get()->keyBy('key'),
            'settings' => $this->settings,
            'faqs' => $this->faqs->highlights(4),
        ];
    }
}
