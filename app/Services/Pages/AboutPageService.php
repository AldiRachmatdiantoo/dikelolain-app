<?php

namespace App\Services\Pages;

use App\Services\FaqService;

class AboutPageService
{
    public function __construct(
        private SectionData $sections,
        private FaqService $faqs,
    ) {
    }

    /**
     * @param  bool  $editing  also list hidden items, and add the editor field definitions
     */
    public function data(bool $editing = false): array
    {
        return [
            ...$this->sections->legacy(),
            ...$this->sections->settings(),
            ...$this->sections->clientLogos($editing),
            'faqs' => $this->faqs->highlights(4),
        ];
    }
}
