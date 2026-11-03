<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/about', function () {
    $sections = App\Models\Section::with('contents.media')->active()->ordered()->get()->keyBy('key');
    return view('pages.about', compact('sections'));
})->name('about');

Route::get('/services', function () {
    $sections = App\Models\Section::with('contents.media')->active()->ordered()->get()->keyBy('key');
    return view('pages.services', compact('sections'));
})->name('services');

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

Route::get('/pricing', function () {
    $sections = App\Models\Section::with('contents.media')->active()->ordered()->get()->keyBy('key');
    return view('pages.pricing', compact('sections'));
})->name('pricing');

Route::get('/faqs', function () {
    $sections = App\Models\Section::with('contents.media')->active()->ordered()->get()->keyBy('key');
    return view('pages.faqs', compact('sections'));
})->name('faqs');

Route::get('/team', function () {
    $sections = App\Models\Section::with('contents.media')->active()->ordered()->get()->keyBy('key');
    return view('pages.team', compact('sections'));
})->name('team');

Route::get('/testimonials', function () {
    $sections = App\Models\Section::with('contents.media')->active()->ordered()->get()->keyBy('key');
    return view('pages.testimonials', compact('sections'));
})->name('testimonials');

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
    return view('pages.404'); // Pastikan Anda membuat pages/404.blade.php
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

            Route::get('/faqs', function () {
                $sections = App\Models\Section::with('contents.media')->active()->ordered()->get()->keyBy('key');
                return view('admin.pages.faqs', compact('sections'));
            })->name('faqs');

            Route::get('/testimonials', function () {
                $sections = App\Models\Section::with('contents.media')->active()->ordered()->get()->keyBy('key');
                return view('admin.pages.testimonials', compact('sections'));
            })->name('testimonials');
        });

        // Portfolio Management
        Route::prefix('portfolio')->name('portfolio.')->group(function () {
            Route::get('/', function () {
                return view('admin.portfolio.index');
            })->name('index');

            Route::get('/create', function () {
                return view('admin.portfolio.create');
            })->name('create');

            Route::get('/{id}/edit', function ($id) {
                return view('admin.portfolio.edit', compact('id'));
            })->name('edit');
        });

        // Services Management
        Route::prefix('services')->name('services.')->group(function () {
            Route::get('/', function () {
                return view('admin.services.index');
            })->name('index');

            Route::get('/create', function () {
                return view('admin.services.create');
            })->name('create');

            Route::get('/{id}/edit', function ($id) {
                return view('admin.services.edit', compact('id'));
            })->name('edit');
        });

        // Team Management
        Route::prefix('team')->name('team.')->group(function () {
            Route::get('/', function () {
                return view('admin.team.index');
            })->name('index');

            Route::get('/create', function () {
                return view('admin.team.create');
            })->name('create');

            Route::get('/{id}/edit', function ($id) {
                return view('admin.team.edit', compact('id'));
            })->name('edit');
        });

        // Testimonials Management
        Route::prefix('testimonials')->name('testimonials.')->group(function () {
            Route::get('/', function () {
                return view('admin.testimonials.index');
            })->name('index');

            Route::get('/create', function () {
                return view('admin.testimonials.create');
            })->name('create');

            Route::get('/{id}/edit', function ($id) {
                return view('admin.testimonials.edit', compact('id'));
            })->name('edit');
        });

        // Settings
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/general', function () {
                return view('admin.settings.general');
            })->name('general');
        });

        // Media Library
        Route::get('/media-library', function () {
            return view('admin.media.library');
        })->name('media.library');

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
