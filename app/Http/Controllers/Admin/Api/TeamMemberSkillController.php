<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Requests\Admin\TeamMemberSkillRequest;
use App\Http\Resources\TeamMemberSkillResource;
use App\Services\ListService;
use App\Services\TeamMemberSkillService;

class TeamMemberSkillController extends ListController
{
    protected string $request = TeamMemberSkillRequest::class;

    protected string $resource = TeamMemberSkillResource::class;

    public function __construct(private TeamMemberSkillService $skills)
    {
    }

    protected function service(): ListService
    {
        return $this->skills;
    }
}
