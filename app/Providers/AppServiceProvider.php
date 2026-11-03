<?php

namespace App\Providers;

use App\Models\Section;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share current font setting to all views
        View::composer('*', function ($view) {
            $currentFont = 'Montserrat'; // Default font
            
            try {
                $settingsSection = Section::where('key', 'site-settings')->first();
                if ($settingsSection) {
                    $fontContent = $settingsSection->contents()->where('key', 'settings_font_family')->first();
                    if ($fontContent && !empty($fontContent->value)) {
                        $currentFont = $fontContent->value;
                    }
                }
            } catch (\Exception $e) {
                // If database not ready, use default
            }
            
            $view->with('currentFont', $currentFont);
        });
    }
}
