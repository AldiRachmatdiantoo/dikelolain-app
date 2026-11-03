<?php

namespace App\View\Composers;

use App\Services\ServiceService;
use App\Services\SettingService;
use Illuminate\View\View;

/**
 * Data for partials/footer.blade.php, which is shown on every public page (404 too).
 */
class FooterComposer
{
    public function __construct(
        private SettingService $settings,
        private ServiceService $services,
    ) {
    }

    public function compose(View $view): void
    {
        $view->with([
            'settings' => $this->settings,
            'footerServices' => $this->services->highlights(4),
        ]);
    }
}
