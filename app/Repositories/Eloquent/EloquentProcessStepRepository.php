<?php

namespace App\Repositories\Eloquent;

use App\Models\ProcessStep;
use App\Repositories\Contracts\ProcessStepRepositoryInterface;

class EloquentProcessStepRepository extends EloquentListRepository implements ProcessStepRepositoryInterface
{
    protected string $model = ProcessStep::class;
}
