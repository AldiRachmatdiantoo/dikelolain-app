<?php

namespace App\Services;

use App\Models\Service;
use App\Repositories\Contracts\ServiceRepositoryInterface;
use App\Services\Concerns\CreatesSlugs;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * The services the business offers, each with a detail page at /pages/service/{slug}.
 */
class ServiceService extends ListService
{
    use CreatesSlugs;

    protected array $images = ['image' => 'services', 'icon' => 'services', 'feature_image' => 'services'];

    public function __construct(private ServiceRepositoryInterface $services, ImageUploadService $uploads)
    {
        parent::__construct($services, $uploads);
    }

    public function create(array $data): Model
    {
        return parent::create([...$data, 'slug' => $this->uniqueSlug($data['name'], 'layanan')]);
    }

    public function findBySlug(string $slug, bool $activeOnly = true): ?Service
    {
        return $this->services->findBySlug($slug, $activeOnly);
    }

    /**
     * The first active services, for the footer.
     *
     * @return Collection<int, Service>
     */
    public function highlights(int $limit): Collection
    {
        return $this->services->all(activeOnly: true)->take($limit)->values()->toBase();
    }

    protected function slugExists(string $slug): bool
    {
        return $this->services->slugExists($slug);
    }
}
