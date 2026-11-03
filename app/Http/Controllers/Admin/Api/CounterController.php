<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Requests\Admin\CounterRequest;
use App\Http\Resources\CounterResource;
use App\Services\ListService;
use App\Services\CounterService;

class CounterController extends ListController
{
    protected string $request = CounterRequest::class;

    protected string $resource = CounterResource::class;

    public function __construct(private CounterService $counters)
    {
    }

    protected function service(): ListService
    {
        return $this->counters;
    }
}
