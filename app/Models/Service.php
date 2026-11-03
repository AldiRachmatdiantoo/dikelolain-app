<?php

namespace App\Models;

use App\Support\Media;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    public const DEFAULT_ICON = 'images/icon-service-1.svg';

    protected $fillable = [
        'slug', 'name', 'summary', 'image', 'icon', 'feature_image', 'description', 'benefits_title', 'benefits_intro', 'sort_order', 'is_active',
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

    public function benefits(): HasMany
    {
        return $this->hasMany(ServiceBenefit::class)->orderBy('sort_order')->orderBy('id');
    }

    public function faqs(): HasMany
    {
        return $this->hasMany(ServiceFaq::class)->orderBy('sort_order')->orderBy('id');
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::get(fn () => Media::url($this->image));
    }

    protected function iconUrl(): Attribute
    {
        return Attribute::get(fn () => Media::url($this->icon ?: self::DEFAULT_ICON));
    }

    /** The detail page image; the card image when none was uploaded. */
    protected function featureImageUrl(): Attribute
    {
        return Attribute::get(fn () => Media::url($this->feature_image ?: $this->image));
    }

    /**
     * The description split on blank lines, one entry per paragraph.
     *
     * @return list<string>
     */
    public function paragraphs(): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\R\s*\R/', (string) $this->description))));
    }
}
