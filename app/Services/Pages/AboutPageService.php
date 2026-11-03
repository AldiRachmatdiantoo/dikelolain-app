<?php

namespace App\Services\Pages;

use App\Models\Section;
use App\Services\ClientLogoService;
use App\Services\FaqService;
use App\Services\SettingService;
use App\Vcms\Fields\ClientLogoFields;

class AboutPageService
{
    public function __construct(
        private FaqService $faqs,
        private ClientLogoService $clientLogos,
        private SettingService $settings,
    ) {
    }

    /**
     * @param  bool  $editing  also list hidden items, and add the editor field definitions
     */
    public function data(bool $editing = false): array
    {
        return [
            // Old key-value content for the sections not migrated yet (Epic 3).
            'sections' => Section::with('contents.media')->active()->ordered()->get()->keyBy('key'),
            'settings' => $this->settings,
            'faqs' => $this->faqs->highlights(4),
            'clientLogos' => $this->clientLogos->all(activeOnly: !$editing),
            'clientLogoFields' => $editing ? ClientLogoFields::definition() : [],
        ];
    }
}
