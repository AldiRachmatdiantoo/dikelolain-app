<?php

namespace App\Repositories\Eloquent;

use App\Models\ServiceFaq;
use App\Repositories\Contracts\ServiceFaqRepositoryInterface;

class EloquentServiceFaqRepository extends EloquentListRepository implements ServiceFaqRepositoryInterface
{
    protected string $model = ServiceFaq::class;

    protected array $groupColumns = ['service_id'];
}
