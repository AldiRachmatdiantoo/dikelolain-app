<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Requests\Admin\TeamMemberRequest;
use App\Http\Resources\TeamMemberResource;
use App\Services\ListService;
use App\Services\TeamMemberService;

class TeamMemberController extends ListController
{
    protected string $request = TeamMemberRequest::class;

    protected string $resource = TeamMemberResource::class;

    public function __construct(private TeamMemberService $members)
    {
    }

    protected function service(): ListService
    {
        return $this->members;
    }
}
