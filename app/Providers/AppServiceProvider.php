<?php

namespace App\Providers;

use App\Services\SettingService;
use App\Vcms\EditMode;
use App\View\Composers\FooterComposer;
use App\View\Composers\SeoComposer;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // One instance per request, so stored settings are read from the database once.
        $this->app->scoped(SettingService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // @vcmsediting ... @endvcmsediting: only rendered for an admin in edit mode.
        Blade::if('vcmsediting', fn () => EditMode::active());

        View::composer('partials.footer', FooterComposer::class);
        View::composer('layouts.app', SeoComposer::class);
    }
}
