<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\ListRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Eloquent base for ordered lists. Subclasses name the model and, if the list
 * is ordered per group, the group columns.
 */
abstract class EloquentListRepository implements ListRepositoryInterface
{
    /** @var class-string<Model> */
    protected string $model;

    /** @var list<string> columns that split the list into separately ordered groups */
    protected array $groupColumns = [];

    public function all(bool $activeOnly = false): Collection
    {
        $query = $this->query()->when($activeOnly, fn (Builder $query) => $query->where('is_active', true));

        foreach ($this->groupColumns as $column) {
            $query->orderBy($column);
        }

        return $this->ordered($query)->get();
    }

    public function findOrFail(int $id): Model
    {
        return $this->query()->findOrFail($id);
    }

    public function siblings(Model $item): Collection
    {
        return $this->ordered($this->inGroup($item->getAttributes()))->get();
    }

    public function maxSortOrder(array $attributes): int
    {
        return (int) $this->inGroup($attributes)->max('sort_order');
    }

    public function changesGroup(Model $item, array $attributes): bool
    {
        foreach ($this->groupColumns as $column) {
            if (array_key_exists($column, $attributes) && $attributes[$column] != $item->getAttribute($column)) {
                return true;
            }
        }

        return false;
    }

    public function create(array $data): Model
    {
        return $this->query()->create($data);
    }

    public function update(Model $item, array $data): Model
    {
        $item->update($data);

        return $item;
    }

    public function delete(Model $item): void
    {
        $item->delete();
    }

    public function setOrder(array $ids): void
    {
        DB::transaction(function () use ($ids) {
            foreach (array_values($ids) as $index => $id) {
                $this->query()->whereKey($id)->update(['sort_order' => $index + 1]);
            }
        });
    }

    protected function query(): Builder
    {
        return $this->model::query();
    }

    private function inGroup(array $attributes): Builder
    {
        $query = $this->query();
        foreach ($this->groupColumns as $column) {
            $query->where($column, $attributes[$column] ?? null);
        }

        return $query;
    }

    private function ordered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy($query->getModel()->getKeyName());
    }
}
