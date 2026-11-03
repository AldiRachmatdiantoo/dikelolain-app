<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Requests\Admin\FaqRequest;
use App\Http\Resources\FaqResource;
use App\Services\FaqService;
use App\Services\ListService;

class FaqController extends ListController
{
    protected string $request = FaqRequest::class;

    protected string $resource = FaqResource::class;

    public function __construct(private FaqService $faqs)
    {
    }

    protected function service(): ListService
    {
        return $this->faqs;
    }
}
