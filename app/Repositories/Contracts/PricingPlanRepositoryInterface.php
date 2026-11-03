<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface PricingPlanRepositoryInterface extends ListRepositoryInterface
{
    /**
     * Plans with their features loaded (only active plans and features when $activeOnly).
     */
    public function allWithFeatures(bool $activeOnly = false): Collection;
}
