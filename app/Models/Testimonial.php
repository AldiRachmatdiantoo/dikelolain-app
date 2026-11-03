<?php

namespace App\Models;

use App\Support\Media;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $fillable = ['name', 'role', 'content', 'rating', 'logo', 'photo', 'sort_order', 'is_active'];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
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

    protected function logoUrl(): Attribute
    {
        return Attribute::get(fn () => Media::url($this->logo));
    }

    protected function photoUrl(): Attribute
    {
        return Attribute::get(fn () => Media::url($this->photo));
    }
}
