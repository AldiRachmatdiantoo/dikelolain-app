<?php

namespace App\Services;

use App\Repositories\Contracts\PricingPlanRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class PricingPlanService extends ListService
{
    public function __construct(private PricingPlanRepositoryInterface $plans, ImageUploadService $uploads)
    {
        parent::__construct($plans, $uploads);
    }

    /**
     * Plans with their features; visitors only see active plans and features.
     */
    public function withFeatures(bool $activeOnly): Collection
    {
        return $this->plans->allWithFeatures($activeOnly);
    }
}
