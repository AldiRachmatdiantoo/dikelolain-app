<?php

namespace App\Services;

use App\Repositories\Contracts\AchievementRepositoryInterface;

class AchievementService extends ListService
{
    public function __construct(AchievementRepositoryInterface $achievements, ImageUploadService $uploads)
    {
        parent::__construct($achievements, $uploads);
    }
}
