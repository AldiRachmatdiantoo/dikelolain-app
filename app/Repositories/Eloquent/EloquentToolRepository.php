<?php

namespace App\Repositories\Eloquent;

use App\Models\Tool;
use App\Repositories\Contracts\ToolRepositoryInterface;

class EloquentToolRepository extends EloquentListRepository implements ToolRepositoryInterface
{
    protected string $model = Tool::class;
}
