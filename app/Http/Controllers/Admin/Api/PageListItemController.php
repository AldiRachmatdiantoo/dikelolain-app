<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Requests\Admin\PageListItemRequest;
use App\Http\Resources\PageListItemResource;
use App\Services\ListService;
use App\Services\PageListItemService;

class PageListItemController extends ListController
{
    protected string $request = PageListItemRequest::class;

    protected string $resource = PageListItemResource::class;

    public function __construct(private PageListItemService $items)
    {
    }

    protected function service(): ListService
    {
        return $this->items;
    }
}
