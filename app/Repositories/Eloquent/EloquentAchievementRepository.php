<?php

namespace App\Repositories\Eloquent;

use App\Models\Achievement;
use App\Repositories\Contracts\AchievementRepositoryInterface;

class EloquentAchievementRepository extends EloquentListRepository implements AchievementRepositoryInterface
{
    protected string $model = Achievement::class;
}
