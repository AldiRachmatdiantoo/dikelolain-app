<?php

namespace App\View\Composers;

use App\Services\SettingService;
use Illuminate\View\View;

/**
 * Page settings for partials shown on every public page. Error pages (404) are not built
 * by a page service, so they would not have $settings otherwise.
 */
class SettingsComposer
{
    public function __construct(private SettingService $settings)
    {
    }

    public function compose(View $view): void
    {
        $view->with('settings', $this->settings);
    }
}
