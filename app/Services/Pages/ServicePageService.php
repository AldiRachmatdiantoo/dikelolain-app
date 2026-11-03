<?php

namespace App\Services\Pages;

use App\Services\ServiceService;
use App\Vcms\Fields\ServiceFields;

class ServicePageService
{
    public function __construct(
        private SectionData $sections,
        private ServiceService $services,
    ) {
    }

    /**
     * @param  bool  $editing  also list hidden items, and add the editor field definitions
     */
    public function data(bool $editing = false): array
    {
        return [
            ...$this->sections->settings(),
            ...$this->sections->tools($editing),
            'services' => $this->services->all(activeOnly: !$editing),
            'serviceFields' => $editing ? ServiceFields::definition() : [],
        ];
    }
}
