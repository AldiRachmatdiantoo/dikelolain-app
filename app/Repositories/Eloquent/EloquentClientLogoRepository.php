<?php

namespace App\Repositories\Eloquent;

use App\Models\ClientLogo;
use App\Repositories\Contracts\ClientLogoRepositoryInterface;

class EloquentClientLogoRepository extends EloquentListRepository implements ClientLogoRepositoryInterface
{
    protected string $model = ClientLogo::class;
}
