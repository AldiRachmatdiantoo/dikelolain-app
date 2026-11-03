<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamMemberSkill extends Model
{
    protected $fillable = ['team_member_id', 'name', 'percent', 'sort_order', 'is_active'];

    protected function casts(): array
    {
        return [
            'team_member_id' => 'integer',
            'percent' => 'integer',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(TeamMember::class, 'team_member_id');
    }
}
