<?php

namespace App\Services\Pages;

use App\Services\ProjectService;
use App\Vcms\Fields\ProjectFields;

/**
 * Portfolio page and its horizontal slider variant (/portfolio-experiment).
 */
class PortfolioPageService
{
    public function __construct(
        private SectionData $sections,
        private ProjectService $projects,
    ) {
    }

    /**
     * @param  bool  $editing  also list hidden items, and add the editor field definitions
     */
    public function data(bool $editing = false): array
    {
        return [
            ...$this->sections->settings(),
            'projects' => $this->projects->all(activeOnly: !$editing),
            'projectFields' => $editing ? ProjectFields::definition() : [],
        ];
    }
}
