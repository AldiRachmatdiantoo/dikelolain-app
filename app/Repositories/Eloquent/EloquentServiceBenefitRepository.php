<?php

namespace App\Repositories\Eloquent;

use App\Models\ServiceBenefit;
use App\Repositories\Contracts\ServiceBenefitRepositoryInterface;

class EloquentServiceBenefitRepository extends EloquentListRepository implements ServiceBenefitRepositoryInterface
{
    protected string $model = ServiceBenefit::class;

    protected array $groupColumns = ['service_id'];
}
