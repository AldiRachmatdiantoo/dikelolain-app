<?php

namespace App\Services;

use App\Models\TeamMember;
use App\Repositories\Contracts\TeamMemberRepositoryInterface;
use App\Services\Concerns\CreatesSlugs;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Team members, each with a detail page at /pages/team/{slug}.
 */
class TeamMemberService extends ListService
{
    use CreatesSlugs;

    protected array $images = ['photo' => 'team'];

    public function __construct(private TeamMemberRepositoryInterface $members, ImageUploadService $uploads)
    {
        parent::__construct($members, $uploads);
    }

    public function create(array $data): Model
    {
        return parent::create([...$data, 'slug' => $this->uniqueSlug($data['name'], 'anggota')]);
    }

    public function findBySlug(string $slug, bool $activeOnly = true): ?TeamMember
    {
        return $this->members->findBySlug($slug, $activeOnly);
    }

    /**
     * The first active members, for teaser sections (Home, About).
     *
     * @return Collection<int, TeamMember>
     */
    public function highlights(int $limit): Collection
    {
        return $this->members->all(activeOnly: true)->take($limit)->values()->toBase();
    }

    protected function slugExists(string $slug): bool
    {
        return $this->members->slugExists($slug);
    }
}
