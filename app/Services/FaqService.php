<?php

namespace App\Services;

use App\Models\Faq;
use App\Repositories\Contracts\FaqRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

/**
 * FAQs are ordered per category: new or re-categorised items go last,
 * and moving swaps an item with its neighbour in the same category.
 */
class FaqService
{
    public function __construct(private FaqRepositoryInterface $faqs)
    {
    }

    /**
     * @return Collection<int, Faq>
     */
    public function all(bool $activeOnly = false): Collection
    {
        return $this->faqs->all($activeOnly);
    }

    /**
     * Active FAQs grouped by category key, categories in display order (empty ones included).
     *
     * @return array<string, Collection<int, Faq>>
     */
    public function grouped(bool $activeOnly = true): array
    {
        $faqs = $this->faqs->all($activeOnly)->groupBy('category');

        $grouped = [];
        foreach (array_keys(Faq::CATEGORIES) as $category) {
            $grouped[$category] = $faqs->get($category, new Collection());
        }

        return $grouped;
    }

    /**
     * The first active FAQs in page order, for teaser sections (About page).
     *
     * @return SupportCollection<int, Faq>
     */
    public function highlights(int $limit): SupportCollection
    {
        return collect($this->grouped())->flatten(1)->take($limit)->values();
    }

    public function find(int $id): Faq
    {
        return $this->faqs->findOrFail($id);
    }

    public function create(array $data): Faq
    {
        $data['sort_order'] = $this->faqs->maxSortOrder($data['category']) + 1;

        return $this->faqs->create($data);
    }

    public function update(Faq $faq, array $data): Faq
    {
        if (isset($data['category']) && $data['category'] !== $faq->category) {
            $data['sort_order'] = $this->faqs->maxSortOrder($data['category']) + 1;
        }

        return $this->faqs->update($faq, $data);
    }

    public function delete(Faq $faq): void
    {
        $this->faqs->delete($faq);
    }

    /**
     * @param  'up'|'down'  $direction
     */
    public function move(Faq $faq, string $direction): void
    {
        $ids = $this->faqs->inCategory($faq->category)->modelKeys();
        $from = array_search($faq->getKey(), $ids, true);
        $to = $direction === 'up' ? $from - 1 : $from + 1;

        if (!isset($ids[$to])) {
            return; // already first/last
        }

        [$ids[$from], $ids[$to]] = [$ids[$to], $ids[$from]];
        $this->faqs->setOrder($ids);
    }
}
