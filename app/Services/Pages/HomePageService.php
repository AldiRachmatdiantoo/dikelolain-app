<?php

namespace App\Services\Pages;

use App\Models\Section;
use App\Services\ClientLogoService;
use App\Services\SettingService;
use App\Vcms\Fields\ClientLogoFields;

class HomePageService
{
    public function __construct(
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
            'clientLogos' => $this->clientLogos->all(activeOnly: !$editing),
            'clientLogoFields' => $editing ? ClientLogoFields::definition() : [],
        ];
    }
}
