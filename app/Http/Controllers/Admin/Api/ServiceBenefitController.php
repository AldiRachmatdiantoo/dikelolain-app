<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Requests\Admin\ServiceBenefitRequest;
use App\Http\Resources\ServiceBenefitResource;
use App\Services\ListService;
use App\Services\ServiceBenefitService;

class ServiceBenefitController extends ListController
{
    protected string $request = ServiceBenefitRequest::class;

    protected string $resource = ServiceBenefitResource::class;

    public function __construct(private ServiceBenefitService $benefits)
    {
    }

    protected function service(): ListService
    {
        return $this->benefits;
    }
}
