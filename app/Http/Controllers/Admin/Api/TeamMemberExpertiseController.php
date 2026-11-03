<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Requests\Admin\TeamMemberExpertiseRequest;
use App\Http\Resources\TeamMemberExpertiseResource;
use App\Services\ListService;
use App\Services\TeamMemberExpertiseService;

class TeamMemberExpertiseController extends ListController
{
    protected string $request = TeamMemberExpertiseRequest::class;

    protected string $resource = TeamMemberExpertiseResource::class;

    public function __construct(private TeamMemberExpertiseService $expertises)
    {
    }

    protected function service(): ListService
    {
        return $this->expertises;
    }
}
