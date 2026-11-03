<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Requests\Admin\TestimonialRequest;
use App\Http\Resources\TestimonialResource;
use App\Services\ListService;
use App\Services\TestimonialService;

class TestimonialController extends ListController
{
    protected string $request = TestimonialRequest::class;

    protected string $resource = TestimonialResource::class;

    public function __construct(private TestimonialService $testimonials)
    {
    }

    protected function service(): ListService
    {
        return $this->testimonials;
    }
}
