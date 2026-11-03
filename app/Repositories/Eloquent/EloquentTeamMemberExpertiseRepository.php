<?php

namespace App\Repositories\Eloquent;

use App\Models\TeamMemberExpertise;
use App\Repositories\Contracts\TeamMemberExpertiseRepositoryInterface;

class EloquentTeamMemberExpertiseRepository extends EloquentListRepository implements TeamMemberExpertiseRepositoryInterface
{
    protected string $model = TeamMemberExpertise::class;

    protected array $groupColumns = ['team_member_id'];
}
