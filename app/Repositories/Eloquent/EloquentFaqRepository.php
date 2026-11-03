<?php

namespace App\Repositories\Eloquent;

use App\Models\Faq;
use App\Repositories\Contracts\FaqRepositoryInterface;

class EloquentFaqRepository extends EloquentListRepository implements FaqRepositoryInterface
{
    protected string $model = Faq::class;

    protected array $groupColumns = ['category'];
}
