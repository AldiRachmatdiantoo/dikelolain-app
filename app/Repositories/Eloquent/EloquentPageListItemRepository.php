<?php

namespace App\Repositories\Eloquent;

use App\Models\PageListItem;
use App\Repositories\Contracts\PageListItemRepositoryInterface;

class EloquentPageListItemRepository extends EloquentListRepository implements PageListItemRepositoryInterface
{
    protected string $model = PageListItem::class;

    protected array $groupColumns = ['list_key'];
}
