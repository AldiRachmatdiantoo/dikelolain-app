<?php

namespace App\Services\Pages;

use App\Services\ProcessStepService;
use App\Vcms\Fields\ProcessStepFields;

class HomePageService
{
    public function __construct(
        private SectionData $sections,
        private ProcessStepService $processSteps,
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
            ...$this->sections->tools($editing),
            'processSteps' => $this->processSteps->all(activeOnly: !$editing),
            'processStepFields' => $editing ? ProcessStepFields::definition() : [],
        ];
    }
}
