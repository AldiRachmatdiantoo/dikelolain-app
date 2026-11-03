<?php

namespace App\Services\Pages;

use App\Services\ProcessStepService;
use App\Services\ProjectService;
use App\Services\ServiceService;
use App\Vcms\Fields\ProcessStepFields;
use App\Vcms\Fields\ProjectFields;
use App\Vcms\Fields\ServiceFields;

class HomePageService
{
    public function __construct(
        private SectionData $sections,
        private ProcessStepService $processSteps,
        private ServiceService $services,
        private ProjectService $projects,
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
            ...$this->sections->achievements($editing),
            ...$this->sections->counters($editing),
            ...$this->sections->team($editing),
            'processSteps' => $this->processSteps->all(activeOnly: !$editing),
            'processStepFields' => $editing ? ProcessStepFields::definition() : [],
            'services' => $this->services->all(activeOnly: !$editing),
            'serviceFields' => $editing ? ServiceFields::definition() : [],
            'projects' => $this->projects->highlights(6),
            'projectFields' => $editing ? ProjectFields::definition() : [],
        ];
    }
}
