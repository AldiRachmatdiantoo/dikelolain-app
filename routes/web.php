<?php

use App\Http\Controllers\Admin\Api\AchievementController;
use App\Http\Controllers\Admin\Api\ClientLogoController;
use App\Http\Controllers\Admin\Api\FaqController;
use App\Http\Controllers\Admin\Api\ProcessStepController;
use App\Http\Controllers\Admin\Api\SettingController;
use App\Http\Controllers\Admin\Api\TestimonialController;
use App\Http\Controllers\Admin\Api\ToolController;
use App\Http\Controllers\Admin\EditModeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Pages\AboutPageController;
use App\Http\Controllers\Pages\FaqPageController;
use App\Http\Controllers\Pages\HomePageController;
use App\Http\Controllers\Pages\PricingPageController;
use App\Http\Controllers\Pages\ServicePageController;
use App\Http\Controllers\Pages\TestimonialPageController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', HomePageController::class)->name('home');

Route::get('/about', AboutPageController::class)->name('about');

Route::get('/services', ServicePageController::class)->name('services');

Route::get('/contact', function () {
    $sections = App\Models\Section::with('contents.media')->active()->ordered()->get()->keyBy('key');
    return view('pages.contact', compact('sections'));
})->name('contact');

Route::get('/portfolio', function () {
    $sections = App\Models\Section::with('contents.media')->active()->ordered()->get()->keyBy('key');
    return view('pages.portfolio', compact('sections'));
})->name('portfolio');

Route::get('/portfolio-experiment', function () {
    $sections = App\Models\Section::with('contents.media')->active()->ordered()->get()->keyBy('key');
    return view('pages.portfolio-experiment', compact('sections'));
})->name('portfolio.experiment');

Route::get('/pricing', PricingPageController::class)->name('pricing');

Route::get('/faqs', FaqPageController::class)->name('faqs');

Route::get('/team', function () {
    $sections = App\Models\Section::with('contents.media')->active()->ordered()->get()->keyBy('key');
    return view('pages.team', compact('sections'));
})->name('team');

Route::get('/testimonials', TestimonialPageController::class)->name('testimonials');

Route::prefix('pages')->name('pages.')->group(function () {
    Route::get('/service/{serviceId}', function ($serviceId) {
        $sections = App\Models\Section::with('contents.media')->active()->ordered()->get()->keyBy('key');
        return view('pages.service-single', compact('sections', 'serviceId'));
    })->name('service.single');

    Route::get('/team/{index}', function ($index) {
        $sections = App\Models\Section::with('contents.media')->active()->ordered()->get()->keyBy('key');
        return view('pages.team-single', compact('sections', 'index'));
    })->name('team.single');
});

// Anda mungkin perlu memperbarui logika 'active' di header.blade.php
// menjadi: request()->routeIs('pages.*') atau sejenisnya.

// Rute untuk 404
Route::fallback(function () {
    return response()->view('pages.404', [], 404);
});




/// Routes admin

Route::prefix('admin')->name('admin.')->group(function () {

    //guest login
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->name('handle-login');
    });

    Route::middleware('auth')->group(function () {

        //change name and email
        Route::patch('/profile', [ProfileController::class, 'updateProfile'])->name('profile.update');
        //reset password
        Route::get('/reset-password', function () {
            return view('admin.auth.reset-password');
        })->name('reset-password');
        // 2. Rute untuk MEMPROSES perubahan password (PUT/PATCH)
        Route::put('/admin/profile/password', [ProfileController::class, 'updatePassword'])
            ->name('password.update.dashboard');
        //profile-detail
        Route::get('/profile-detail', function () {
            return view('admin.pages.profile-detail');
        })->name('profile-detail');
        // Logout
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

        // VCMS: edit mode on the public pages
        Route::get('/edit-mode', [EditModeController::class, 'enable'])->name('edit-mode.on');
        Route::post('/edit-mode/off', [EditModeController::class, 'disable'])->name('edit-mode.off');

        // VCMS API (ARCHITECTURE.md §5)
        Route::prefix('api')->name('api.')->group(function () {
            Route::get('/settings/{key}', [SettingController::class, 'show'])->name('settings.show');
            Route::patch('/settings/{key}', [SettingController::class, 'update'])->name('settings.update');

            // Ordered lists: index, store, update, destroy, move (Admin\Api\ListController)
            $lists = [
                'faqs' => FaqController::class,
                'testimonials' => TestimonialController::class,
                'client-logos' => ClientLogoController::class,
                'tools' => ToolController::class,
                'process-steps' => ProcessStepController::class,
                'achievements' => AchievementController::class,
            ];
            foreach ($lists as $uri => $controller) {
                Route::get("/{$uri}", [$controller, 'index'])->name("{$uri}.index");
                Route::post("/{$uri}", [$controller, 'store'])->name("{$uri}.store");
                Route::patch("/{$uri}/{id}", [$controller, 'update'])->whereNumber('id')->name("{$uri}.update");
                Route::delete("/{$uri}/{id}", [$controller, 'destroy'])->whereNumber('id')->name("{$uri}.destroy");
                Route::post("/{$uri}/{id}/move", [$controller, 'move'])->whereNumber('id')->name("{$uri}.move");
            }
        });

        // Dashboard
        Route::get('/dashboard', function () {
            $sections = App\Models\Section::with('contents.media')
                ->active()
                ->ordered()
                ->get()
                ->keyBy('key');

            return view('admin.pages.home', compact('sections'));
        })->name('dashboard');

        // Pages Management (VCMS Editor)
        Route::prefix('pages')->name('pages.')->group(function () {
            Route::get('/analytics', function () {
                $sections = App\Models\Section::with('contents')
                    ->active()->ordered()->get()->keyBy('key');
                return view('admin.pages.analytics', compact('sections'));
            })->name('analytics');

            Route::get('/home', function () {
                $sections = App\Models\Section::with('contents.media')
                    ->active()
                    ->ordered()
                    ->get()
                    ->keyBy('key');

                return view('admin.pages.home', compact('sections'));
            })->name('home');

            Route::get('/about', function () {
                $sections = App\Models\Section::with('contents.media')->active()->ordered()->get()->keyBy('key');
                return view('admin.pages.about', compact('sections'));
            })->name('about');

            Route::get('/services', function () {
                $sections = App\Models\Section::with('contents.media')->active()->ordered()->get()->keyBy('key');
                return view('admin.pages.services', compact('sections'));
            })->name('services');

            Route::get('/service/{serviceId}', function ($serviceId) {
                $sections = App\Models\Section::with('contents.media')->active()->ordered()->get()->keyBy('key');
                return view('admin.pages.service-detail', compact('sections', 'serviceId'));
            })->name('service.detail');

            Route::get('/portfolio', function () {
                $sections = App\Models\Section::with('contents.media')->active()->ordered()->get()->keyBy('key');
                return view('admin.pages.portfolio', compact('sections'));
            })->name('portfolio');

            Route::get('/team', function () {
                $sections = App\Models\Section::with('contents.media')->active()->ordered()->get()->keyBy('key');
                return view('admin.pages.team', compact('sections'));
            })->name('team');

            Route::get('/pricing', function () {
                $sections = App\Models\Section::with('contents.media')->active()->ordered()->get()->keyBy('key');
                return view('admin.pages.pricing', compact('sections'));
            })->name('pricing');

            Route::get('/contact', function () {
                $sections = App\Models\Section::with('contents.media')->active()->ordered()->get()->keyBy('key');
                return view('admin.pages.contact', compact('sections'));
            })->name('contact');

            Route::get('/testimonials', function () {
                $sections = App\Models\Section::with('contents.media')->active()->ordered()->get()->keyBy('key');
                return view('admin.pages.testimonials', compact('sections'));
            })->name('testimonials');
        });

        // API Routes for Content Management
        Route::prefix('api/content')->name('api.content.')->group(function () {
            Route::post('/update', [App\Http\Controllers\Admin\ContentController::class, 'updateContent'])->name('update');
            Route::post('/batch-update', [App\Http\Controllers\Admin\ContentController::class, 'batchUpdate'])->name('batch-update');
            Route::post('/upload', [App\Http\Controllers\Admin\ContentController::class, 'uploadFile'])->name('upload');
            Route::get('/media-list', [App\Http\Controllers\Admin\ContentController::class, 'getMediaList'])->name('media.list');
            Route::get('/{sectionKey}', [App\Http\Controllers\Admin\ContentController::class, 'getContent'])->name('get');
            Route::delete('/media/{id}', [App\Http\Controllers\Admin\ContentController::class, 'deleteMedia'])->name('media.delete');
        });
    });
});
