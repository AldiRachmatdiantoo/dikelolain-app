<?php

namespace App\Services;

use App\Repositories\Contracts\ListRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

/**
 * Rules shared by every ordered list: new or regrouped items go last in their
 * group, moving swaps an item with its neighbour, and uploaded images replace
 * (and delete) the previous file.
 */
abstract class ListService
{
    /** @var array<string, string> image column => upload folder */
    protected array $images = [];

    public function __construct(
        protected ListRepositoryInterface $items,
        protected ImageUploadService $uploads,
    ) {
    }

    public function all(bool $activeOnly = false): Collection
    {
        return $this->items->all($activeOnly);
    }

    public function find(int $id): Model
    {
        return $this->items->findOrFail($id);
    }

    public function create(array $data): Model
    {
        $data = $this->storeImages($data);
        $data['sort_order'] = $this->items->maxSortOrder($data) + 1;

        return $this->items->create($data);
    }

    public function update(Model $item, array $data): Model
    {
        $previous = $item->only(array_keys($this->images));
        $data = $this->storeImages($data);

        if ($this->items->changesGroup($item, $data)) {
            $data['sort_order'] = $this->items->maxSortOrder([...$item->getAttributes(), ...$data]) + 1;
        }

        $item = $this->items->update($item, $data);

        foreach ($previous as $column => $path) {
            if (array_key_exists($column, $data) && $data[$column] !== $path) {
                $this->uploads->delete($path);
            }
        }

        return $item;
    }

    public function delete(Model $item): void
    {
        $this->items->delete($item);

        foreach (array_keys($this->images) as $column) {
            $this->uploads->delete($item->getAttribute($column));
        }
    }

    /**
     * @param  'up'|'down'  $direction
     */
    public function move(Model $item, string $direction): void
    {
        $ids = $this->items->siblings($item)->modelKeys();
        $from = array_search($item->getKey(), $ids, true);
        $to = $direction === 'up' ? $from - 1 : $from + 1;

        if (!isset($ids[$to])) {
            return; // already first/last
        }

        [$ids[$from], $ids[$to]] = [$ids[$to], $ids[$from]];
        $this->items->setOrder($ids);
    }

    private function storeImages(array $data): array
    {
        foreach ($this->images as $column => $folder) {
            if (($data[$column] ?? null) instanceof UploadedFile) {
                $data[$column] = $this->uploads->store($data[$column], $folder);
            }
        }

        return $data;
    }
}
