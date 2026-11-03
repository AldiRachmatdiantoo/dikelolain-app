<?php

namespace App\Services;

use App\Repositories\Contracts\PageListItemRepositoryInterface;
use Illuminate\Support\Collection;

/**
 * Ordered per list (PageListItem::LISTS).
 */
class PageListItemService extends ListService
{
    protected array $images = ['icon' => 'lists'];

    public function __construct(PageListItemRepositoryInterface $items, ImageUploadService $uploads)
    {
        parent::__construct($items, $uploads);
    }

    /**
     * The items of the given lists, keyed by list (an empty collection for a list without items).
     *
     * @param  list<string>  $lists
     * @return array<string, Collection>
     */
    public function lists(array $lists, bool $activeOnly): array
    {
        $items = $this->all($activeOnly)->toBase()->groupBy('list_key');

        return collect($lists)->mapWithKeys(fn (string $list) => [$list => $items->get($list, collect())])->all();
    }
}
