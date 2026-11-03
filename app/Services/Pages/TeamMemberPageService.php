<?php

namespace App\Services\Pages;

use App\Services\TeamMemberService;
use App\Vcms\Fields\TeamMemberExpertiseFields;
use App\Vcms\Fields\TeamMemberFields;
use App\Vcms\Fields\TeamMemberSkillFields;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TeamMemberPageService
{
    public function __construct(private TeamMemberService $teamMembers)
    {
    }

    /**
     * @param  bool  $editing  also show a hidden member and hidden skills/expertises, and add the editor field definitions
     *
     * @throws NotFoundHttpException for an unknown slug, or a hidden member outside edit mode
     */
    public function data(string $slug, bool $editing = false): array
    {
        $member = $this->teamMembers->findBySlug($slug, activeOnly: !$editing)
            ?? throw new NotFoundHttpException();

        return [
            'member' => $member,
            'memberFields' => $editing ? TeamMemberFields::definition() : [],
            'skillFields' => $editing ? TeamMemberSkillFields::definition() : [],
            'expertiseFields' => $editing ? TeamMemberExpertiseFields::definition() : [],
        ];
    }
}
