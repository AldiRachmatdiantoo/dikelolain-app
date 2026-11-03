<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Requests\Admin\ServiceFaqRequest;
use App\Http\Resources\ServiceFaqResource;
use App\Services\ListService;
use App\Services\ServiceFaqService;

class ServiceFaqController extends ListController
{
    protected string $request = ServiceFaqRequest::class;

    protected string $resource = ServiceFaqResource::class;

    public function __construct(private ServiceFaqService $faqs)
    {
    }

    protected function service(): ListService
    {
        return $this->faqs;
    }
}
