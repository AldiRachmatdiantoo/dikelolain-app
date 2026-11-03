<?php

namespace App\Repositories\Eloquent;

use App\Models\Faq;
use App\Repositories\Contracts\FaqRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class EloquentFaqRepository implements FaqRepositoryInterface
{
    public function all(bool $activeOnly = false): Collection
    {
        return Faq::query()
            ->when($activeOnly, fn ($query) => $query->active())
            ->orderBy('category')
            ->ordered()
            ->get();
    }

    public function inCategory(string $category): Collection
    {
        return Faq::where('category', $category)->ordered()->get();
    }

    public function findOrFail(int $id): Faq
    {
        return Faq::findOrFail($id);
    }

    public function maxSortOrder(string $category): int
    {
        return (int) Faq::where('category', $category)->max('sort_order');
    }

    public function create(array $data): Faq
    {
        return Faq::create($data);
    }

    public function update(Faq $faq, array $data): Faq
    {
        $faq->update($data);

        return $faq;
    }

    public function delete(Faq $faq): void
    {
        $faq->delete();
    }

    public function setOrder(array $ids): void
    {
        DB::transaction(function () use ($ids) {
            foreach (array_values($ids) as $index => $id) {
                Faq::whereKey($id)->update(['sort_order' => $index + 1]);
            }
        });
    }
}
