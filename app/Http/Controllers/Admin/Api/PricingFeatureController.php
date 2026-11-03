<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Requests\Admin\PricingFeatureRequest;
use App\Http\Resources\PricingFeatureResource;
use App\Services\ListService;
use App\Services\PricingFeatureService;

class PricingFeatureController extends ListController
{
    protected string $request = PricingFeatureRequest::class;

    protected string $resource = PricingFeatureResource::class;

    public function __construct(private PricingFeatureService $features)
    {
    }

    protected function service(): ListService
    {
        return $this->features;
    }
}
