<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MoveRequest;
use App\Http\Requests\Admin\StoreFaqRequest;
use App\Http\Requests\Admin\UpdateFaqRequest;
use App\Http\Resources\FaqResource;
use App\Services\FaqService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class FaqController extends Controller
{
    public function __construct(private FaqService $faqs)
    {
    }

    public function index(): AnonymousResourceCollection
    {
        return FaqResource::collection($this->faqs->all());
    }

    public function store(StoreFaqRequest $request): JsonResponse
    {
        return (new FaqResource($this->faqs->create($request->validated())))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateFaqRequest $request, int $faq): FaqResource
    {
        return new FaqResource($this->faqs->update($this->faqs->find($faq), $request->validated()));
    }

    public function destroy(int $faq): Response
    {
        $this->faqs->delete($this->faqs->find($faq));

        return response()->noContent();
    }

    public function move(MoveRequest $request, int $faq): Response
    {
        $this->faqs->move($this->faqs->find($faq), $request->validated('direction'));

        return response()->noContent();
    }
}
