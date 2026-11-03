<?php

namespace App\Repositories\Contracts;

use App\Models\TeamMember;

interface TeamMemberRepositoryInterface extends ListRepositoryInterface
{
    /**
     * A member with skills and expertises loaded (only active ones when $activeOnly).
     */
    public function findBySlug(string $slug, bool $activeOnly = true): ?TeamMember;

    public function slugExists(string $slug): bool;
}
