<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Requests\Admin\ProcessStepRequest;
use App\Http\Resources\ProcessStepResource;
use App\Services\ListService;
use App\Services\ProcessStepService;

class ProcessStepController extends ListController
{
    protected string $request = ProcessStepRequest::class;

    protected string $resource = ProcessStepResource::class;

    public function __construct(private ProcessStepService $steps)
    {
    }

    protected function service(): ListService
    {
        return $this->steps;
    }
}
