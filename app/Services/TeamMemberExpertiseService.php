<?php

namespace App\Services;

use App\Repositories\Contracts\TeamMemberExpertiseRepositoryInterface;

/**
 * Ordered per team member.
 */
class TeamMemberExpertiseService extends ListService
{
    public function __construct(TeamMemberExpertiseRepositoryInterface $items, ImageUploadService $uploads)
    {
        parent::__construct($items, $uploads);
    }
}
