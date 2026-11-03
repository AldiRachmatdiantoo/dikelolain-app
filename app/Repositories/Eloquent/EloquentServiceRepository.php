<?php

namespace App\Repositories\Eloquent;

use App\Models\Service;
use App\Repositories\Contracts\ServiceRepositoryInterface;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EloquentServiceRepository extends EloquentListRepository implements ServiceRepositoryInterface
{
    protected string $model = Service::class;

    public function findBySlug(string $slug, bool $activeOnly = true): ?Service
    {
        $onlyActive = fn (HasMany $query) => $query->when($activeOnly, fn ($query) => $query->where('is_active', true));

        return $this->query()
            ->where('slug', $slug)
            ->when($activeOnly, fn ($query) => $query->where('is_active', true))
            ->with(['benefits' => $onlyActive, 'faqs' => $onlyActive])
            ->first();
    }

    public function slugExists(string $slug): bool
    {
        return $this->query()->where('slug', $slug)->exists();
    }
}
