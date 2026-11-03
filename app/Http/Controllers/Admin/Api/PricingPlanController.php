<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Requests\Admin\PricingPlanRequest;
use App\Http\Resources\PricingPlanResource;
use App\Services\ListService;
use App\Services\PricingPlanService;

class PricingPlanController extends ListController
{
    protected string $request = PricingPlanRequest::class;

    protected string $resource = PricingPlanResource::class;

    public function __construct(private PricingPlanService $plans)
    {
    }

    protected function service(): ListService
    {
        return $this->plans;
    }
}
