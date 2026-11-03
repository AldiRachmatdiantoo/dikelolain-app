<?php

namespace App\Services;

use App\Models\TeamMember;
use App\Repositories\Contracts\TeamMemberRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Team members. The slug (used in /pages/team/{slug}) is made from the name
 * once, when the member is created, so links and the editor's reload keep working
 * after a rename.
 */
class TeamMemberService extends ListService
{
    protected array $images = ['photo' => 'team'];

    public function __construct(private TeamMemberRepositoryInterface $members, ImageUploadService $uploads)
    {
        parent::__construct($members, $uploads);
    }

    public function create(array $data): Model
    {
        return parent::create([...$data, 'slug' => $this->uniqueSlug($data['name'])]);
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

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'anggota';
        $slug = $base;

        for ($i = 2; $this->members->slugExists($slug); $i++) {
            $slug = "{$base}-{$i}";
        }

        return $slug;
    }
}
