<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    /**
     * Category key => anchor on the FAQ page, in display order.
     * Category titles are page settings (faqs.category.{key}).
     */
    public const CATEGORIES = [
        'general' => 'general_information',
        'technical' => 'technical_and_design',
        'project' => 'project_management',
        'support' => 'client_support',
    ];

    protected $fillable = ['category', 'question', 'answer', 'sort_order', 'is_active'];

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
}
