<?php

namespace App\Services;

use App\Models\Faq;
use App\Repositories\Contracts\FaqRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

/**
 * FAQs are ordered per category (ListService handles adding, moving, deleting).
 */
class FaqService extends ListService
{
    public function __construct(FaqRepositoryInterface $faqs, ImageUploadService $uploads)
    {
        parent::__construct($faqs, $uploads);
    }

    /**
     * FAQs grouped by category key, categories in display order (empty ones included).
     *
     * @return array<string, Collection<int, Faq>>
     */
    public function grouped(bool $activeOnly = true): array
    {
        $faqs = $this->items->all($activeOnly)->groupBy('category');

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
}
