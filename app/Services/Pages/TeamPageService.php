<?php

namespace App\Services\Pages;

use App\Services\TeamMemberService;
use App\Vcms\Fields\TeamMemberFields;

class TeamPageService
{
    public function __construct(
        private SectionData $sections,
        private TeamMemberService $teamMembers,
    ) {
    }

    /**
     * @param  bool  $editing  also list hidden members, and add the editor field definitions
     */
    public function data(bool $editing = false): array
    {
        return [
            ...$this->sections->settings(),
            'teamMembers' => $this->teamMembers->all(activeOnly: !$editing),
            'teamMemberFields' => $editing ? TeamMemberFields::definition() : [],
        ];
    }
}
