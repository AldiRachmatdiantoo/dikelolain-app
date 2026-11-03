<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Requests\Admin\ProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Services\ListService;
use App\Services\ProjectService;

class ProjectController extends ListController
{
    protected string $request = ProjectRequest::class;

    protected string $resource = ProjectResource::class;

    public function __construct(private ProjectService $projects)
    {
    }

    protected function service(): ListService
    {
        return $this->projects;
    }
}
