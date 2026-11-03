<?php

namespace App\Repositories\Eloquent;

use App\Models\PricingPlan;
use App\Repositories\Contracts\PricingPlanRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EloquentPricingPlanRepository extends EloquentListRepository implements PricingPlanRepositoryInterface
{
    protected string $model = PricingPlan::class;

    public function allWithFeatures(bool $activeOnly = false): Collection
    {
        return $this->all($activeOnly)->load([
            'features' => fn (HasMany $query) => $query->when($activeOnly, fn ($query) => $query->where('is_active', true)),
        ]);
    }
}
