<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Requests\Admin\ClientLogoRequest;
use App\Http\Resources\ClientLogoResource;
use App\Services\ClientLogoService;
use App\Services\ListService;

class ClientLogoController extends ListController
{
    protected string $request = ClientLogoRequest::class;

    protected string $resource = ClientLogoResource::class;

    public function __construct(private ClientLogoService $logos)
    {
    }

    protected function service(): ListService
    {
        return $this->logos;
    }
}
