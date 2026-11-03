<?php

namespace App\Repositories\Eloquent;

use App\Models\PricingFeature;
use App\Repositories\Contracts\PricingFeatureRepositoryInterface;

class EloquentPricingFeatureRepository extends EloquentListRepository implements PricingFeatureRepositoryInterface
{
    protected string $model = PricingFeature::class;

    protected array $groupColumns = ['pricing_plan_id'];
}
