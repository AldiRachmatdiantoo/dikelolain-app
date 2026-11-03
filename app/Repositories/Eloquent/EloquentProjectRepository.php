<?php

namespace App\Repositories\Eloquent;

use App\Models\Project;
use App\Repositories\Contracts\ProjectRepositoryInterface;

class EloquentProjectRepository extends EloquentListRepository implements ProjectRepositoryInterface
{
    protected string $model = Project::class;
}
