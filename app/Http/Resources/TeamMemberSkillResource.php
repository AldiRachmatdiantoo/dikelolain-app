<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\TeamMemberSkill
 */
class TeamMemberSkillResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'team_member_id' => $this->team_member_id,
            'name' => $this->name,
            'percent' => $this->percent,
            'sort_order' => $this->sort_order,
            'is_active' => $this->is_active,
        ];
    }
}
