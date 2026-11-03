<?php

namespace App\Services;

use App\Repositories\Contracts\TeamMemberSkillRepositoryInterface;

/**
 * Ordered per team member.
 */
class TeamMemberSkillService extends ListService
{
    public function __construct(TeamMemberSkillRepositoryInterface $items, ImageUploadService $uploads)
    {
        parent::__construct($items, $uploads);
    }
}
