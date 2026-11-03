<?php

namespace App\Repositories\Contracts;

use App\Models\Service;

interface ServiceRepositoryInterface extends ListRepositoryInterface
{
    /**
     * A service with benefits and FAQs loaded (only active ones when $activeOnly).
     */
    public function findBySlug(string $slug, bool $activeOnly = true): ?Service;

    public function slugExists(string $slug): bool;
}
