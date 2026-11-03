<?php

namespace App\Models;

use App\Support\Media;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TeamMember extends Model
{
    protected $fillable = [
        'slug', 'name', 'role', 'photo', 'experience', 'about', 'expertise_intro', 'skills_intro', 'sort_order', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('sort_order')->orderBy('id');
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    public function skills(): HasMany
    {
        return $this->hasMany(TeamMemberSkill::class)->orderBy('sort_order')->orderBy('id');
    }

    public function expertises(): HasMany
    {
        return $this->hasMany(TeamMemberExpertise::class)->orderBy('sort_order')->orderBy('id');
    }

    protected function photoUrl(): Attribute
    {
        return Attribute::get(fn () => Media::url($this->photo));
    }
}
