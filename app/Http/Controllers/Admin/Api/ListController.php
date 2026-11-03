<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MoveRequest;
use App\Services\ListService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;

/**
 * Admin API for an ordered list (ARCHITECTURE.md §5.1). Subclasses name the
 * service, the form request (store + update) and the API resource.
 */
abstract class ListController extends Controller
{
    /** @var class-string<\App\Http\Requests\Admin\ListItemRequest> */
    protected string $request;

    /** @var class-string<JsonResource> */
    protected string $resource;

    abstract protected function service(): ListService;

    public function index(): AnonymousResourceCollection
    {
        return $this->resource::collection($this->service()->all());
    }

    public function store(): JsonResponse
    {
        $item = $this->service()->create($this->validated());

        return (new $this->resource($item))->response()->setStatusCode(201);
    }

    public function update(int $id): JsonResource
    {
        $service = $this->service();
        $item = $service->find($id);

        return new $this->resource($service->update($item, $this->validated()));
    }

    public function destroy(int $id): Response
    {
        $service = $this->service();
        $service->delete($service->find($id));

        return response()->noContent();
    }

    public function move(MoveRequest $request, int $id): Response
    {
        $service = $this->service();
        $service->move($service->find($id), $request->validated('direction'));

        return response()->noContent();
    }

    /**
     * Resolving a form request validates it (422 on failure).
     */
    private function validated(): array
    {
        return app($this->request)->validated();
    }
}
