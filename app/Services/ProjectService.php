<?php

namespace App\Services;

use App\Repositories\Contracts\ProjectRepositoryInterface;
use Illuminate\Support\Collection;

class ProjectService extends ListService
{
    protected array $images = ['image' => 'projects'];

    public function __construct(ProjectRepositoryInterface $projects, ImageUploadService $uploads)
    {
        parent::__construct($projects, $uploads);
    }

    /**
     * The Home selection: the first active videos, then the first active designs,
     * at most $perType of each.
     */
    public function highlights(int $perType): Collection
    {
        [$videos, $images] = $this->all(activeOnly: true)->toBase()->partition->isVideo();

        return $videos->take($perType)->concat($images->take($perType))->values();
    }
}
