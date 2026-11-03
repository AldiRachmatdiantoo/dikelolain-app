<?php

namespace App\Repositories\Contracts;

use App\Models\Faq;
use Illuminate\Database\Eloquent\Collection;

interface FaqRepositoryInterface
{
    /**
     * All FAQs in order (by category, then position).
     *
     * @return Collection<int, Faq>
     */
    public function all(bool $activeOnly = false): Collection;

    /**
     * @return Collection<int, Faq>
     */
    public function inCategory(string $category): Collection;

    public function findOrFail(int $id): Faq;

    public function maxSortOrder(string $category): int;

    public function create(array $data): Faq;

    public function update(Faq $faq, array $data): Faq;

    public function delete(Faq $faq): void;

    /**
     * Gives the FAQs the positions 1..n in the order of the ids.
     *
     * @param  list<int>  $ids
     */
    public function setOrder(array $ids): void;
}
