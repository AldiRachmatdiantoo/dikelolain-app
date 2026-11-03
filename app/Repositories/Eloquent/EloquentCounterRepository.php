<?php

namespace App\Repositories\Eloquent;

use App\Models\Counter;
use App\Repositories\Contracts\CounterRepositoryInterface;

class EloquentCounterRepository extends EloquentListRepository implements CounterRepositoryInterface
{
    protected string $model = Counter::class;
}
