<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Requests\Admin\ServiceRequest;
use App\Http\Resources\ServiceResource;
use App\Services\ListService;
use App\Services\ServiceService;

class ServiceController extends ListController
{
    protected string $request = ServiceRequest::class;

    protected string $resource = ServiceResource::class;

    public function __construct(private ServiceService $services)
    {
    }

    protected function service(): ListService
    {
        return $this->services;
    }
}
