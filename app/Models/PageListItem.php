<?php

namespace App\Models;

use App\Support\Media;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

/**
 * A line in one of the short bullet lists on About and Pricing.
 */
class PageListItem extends Model
{
    /** list_key => where the list is shown */
    public const LISTS = [
        'about' => 'About: poin di bagian about us',
        'vision' => 'About: daftar visi',
        'mission' => 'About: daftar misi',
        'pricing_benefits' => 'Pricing: keuntungan di bawah paket',
    ];

    protected $fillable = ['list_key', 'text', 'icon', 'sort_order', 'is_active'];

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

    protected function iconUrl(): Attribute
    {
        return Attribute::get(fn () => Media::url($this->icon));
    }
}
