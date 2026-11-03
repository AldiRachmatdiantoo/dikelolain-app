<?php

namespace App\View\Composers;

use App\Services\SettingService;
use Illuminate\View\View;

/**
 * Meta description and preview image for layouts/app.blade.php. Each main page has its
 * own description setting (seo.{route name}.description); other pages use the Home one,
 * and detail pages override it with their own text.
 */
class SeoComposer
{
    public function __construct(private SettingService $settings)
    {
    }

    public function compose(View $view): void
    {
        $key = 'seo.' . request()->route()?->getName() . '.description';
        $ownKey = $this->settings->exists($key);

        $view->with('seo', [
            'description' => $this->settings->get($ownKey ? $key : 'seo.home.description'),
            'image' => $this->settings->url('seo.image'),
            // Settings the admin bar's SEO button edits (only on pages with their own description).
            'keys' => $ownKey ? [$key, 'seo.image'] : [],
        ]);
    }
}
