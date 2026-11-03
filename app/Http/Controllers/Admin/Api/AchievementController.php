<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Requests\Admin\AchievementRequest;
use App\Http\Resources\AchievementResource;
use App\Services\ListService;
use App\Services\AchievementService;

class AchievementController extends ListController
{
    protected string $request = AchievementRequest::class;

    protected string $resource = AchievementResource::class;

    public function __construct(private AchievementService $achievements)
    {
    }

    protected function service(): ListService
    {
        return $this->achievements;
    }
}
