<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\TeamMember
 */
class TeamMemberResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'role' => $this->role,
            'photo' => $this->photo,
            'photo_url' => $this->photo_url,
            'experience' => $this->experience,
            'about' => $this->about,
            'expertise_intro' => $this->expertise_intro,
            'skills_intro' => $this->skills_intro,
            'sort_order' => $this->sort_order,
            'is_active' => $this->is_active,
        ];
    }
}
