<?php

namespace App\Services\Concerns;

use Illuminate\Support\Str;

/**
 * Slugs for detail pages, made from the name once when the item is created
 * (renaming keeps the slug, so links and the editor's reload keep working).
 */
trait CreatesSlugs
{
    abstract protected function slugExists(string $slug): bool;

    /**
     * The name as a slug, made unique with -2, -3, ...
     */
    protected function uniqueSlug(string $name, string $fallback): string
    {
        $base = Str::slug($name) ?: $fallback;
        $slug = $base;

        for ($i = 2; $this->slugExists($slug); $i++) {
            $slug = "{$base}-{$i}";
        }

        return $slug;
    }
}
