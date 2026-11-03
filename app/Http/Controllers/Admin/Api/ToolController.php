<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Requests\Admin\ToolRequest;
use App\Http\Resources\ToolResource;
use App\Services\ListService;
use App\Services\ToolService;

class ToolController extends ListController
{
    protected string $request = ToolRequest::class;

    protected string $resource = ToolResource::class;

    public function __construct(private ToolService $tools)
    {
    }

    protected function service(): ListService
    {
        return $this->tools;
    }
}
