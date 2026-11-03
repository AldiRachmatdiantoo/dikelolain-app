<?php

namespace App\Repositories\Eloquent;

use App\Models\TeamMemberSkill;
use App\Repositories\Contracts\TeamMemberSkillRepositoryInterface;

class EloquentTeamMemberSkillRepository extends EloquentListRepository implements TeamMemberSkillRepositoryInterface
{
    protected string $model = TeamMemberSkill::class;

    protected array $groupColumns = ['team_member_id'];
}
