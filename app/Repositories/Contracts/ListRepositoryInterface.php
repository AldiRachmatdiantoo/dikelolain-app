<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * An ordered list of items (`sort_order`, `is_active`), optionally split into
 * groups that are ordered separately (e.g. FAQs per category).
 */
interface ListRepositoryInterface
{
    /**
     * All items in order (by group, then position).
     */
    public function all(bool $activeOnly = false): Collection;

    public function findOrFail(int $id): Model;

    /**
     * Items in the same group as $item, in order.
     */
    public function siblings(Model $item): Collection;

    /**
     * Highest position in the group the attributes belong to.
     */
    public function maxSortOrder(array $attributes): int;

    /**
     * Whether saving $attributes would move $item to another group.
     */
    public function changesGroup(Model $item, array $attributes): bool;

    public function create(array $data): Model;

    public function update(Model $item, array $data): Model;

    public function delete(Model $item): void;

    /**
     * Gives the items the positions 1..n in the order of the ids.
     *
     * @param  list<int>  $ids
     */
    public function setOrder(array $ids): void;
}
