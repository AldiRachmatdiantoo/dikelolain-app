<?php

namespace App\Repositories\Eloquent;

use App\Models\TeamMember;
use App\Repositories\Contracts\TeamMemberRepositoryInterface;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EloquentTeamMemberRepository extends EloquentListRepository implements TeamMemberRepositoryInterface
{
    protected string $model = TeamMember::class;

    public function findBySlug(string $slug, bool $activeOnly = true): ?TeamMember
    {
        $onlyActive = fn (HasMany $query) => $query->when($activeOnly, fn ($query) => $query->where('is_active', true));

        return $this->query()
            ->where('slug', $slug)
            ->when($activeOnly, fn ($query) => $query->where('is_active', true))
            ->with(['skills' => $onlyActive, 'expertises' => $onlyActive])
            ->first();
    }

    public function slugExists(string $slug): bool
    {
        return $this->query()->where('slug', $slug)->exists();
    }
}
