<?php

namespace App\Services\Pages;

use App\Services\FaqService;
use App\Services\PageListItemService;
use App\Vcms\Fields\PageListItemFields;

class AboutPageService
{
    public function __construct(
        private SectionData $sections,
        private FaqService $faqs,
        private PageListItemService $listItems,
    ) {
    }

    /**
     * @param  bool  $editing  also list hidden items, and add the editor field definitions
     */
    public function data(bool $editing = false): array
    {
        return [
            ...$this->sections->settings(),
            ...$this->sections->clientLogos($editing),
            ...$this->sections->achievements($editing),
            ...$this->sections->counters($editing),
            ...$this->sections->team($editing),
            'faqs' => $this->faqs->highlights(4),
            'lists' => $this->listItems->lists(['about', 'vision', 'mission'], activeOnly: !$editing),
            'listItemFields' => $editing ? PageListItemFields::definition() : [],
        ];
    }
}
