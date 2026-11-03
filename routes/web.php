<?php

use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\Api\AchievementController;
use App\Http\Controllers\Admin\Api\ClientLogoController;
use App\Http\Controllers\Admin\Api\CounterController;
use App\Http\Controllers\Admin\Api\FaqController;
use App\Http\Controllers\Admin\Api\PageListItemController;
use App\Http\Controllers\Admin\Api\PricingFeatureController;
use App\Http\Controllers\Admin\Api\PricingPlanController;
use App\Http\Controllers\Admin\Api\ProcessStepController;
use App\Http\Controllers\Admin\Api\ProjectController;
use App\Http\Controllers\Admin\Api\ServiceBenefitController;
use App\Http\Controllers\Admin\Api\ServiceController;
use App\Http\Controllers\Admin\Api\ServiceFaqController;
use App\Http\Controllers\Admin\Api\SettingController;
use App\Http\Controllers\Admin\Api\TeamMemberController;
use App\Http\Controllers\Admin\Api\TeamMemberExpertiseController;
use App\Http\Controllers\Admin\Api\TeamMemberSkillController;
use App\Http\Controllers\Admin\Api\TestimonialController;
use App\Http\Controllers\Admin\Api\ToolController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EditModeController;
use App\Http\Controllers\Admin\MessageController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Pages\AboutPageController;
use App\Http\Controllers\Pages\ContactPageController;
use App\Http\Controllers\Pages\FaqPageController;
use App\Http\Controllers\Pages\HomePageController;
use App\Http\Controllers\Pages\NotFoundPageController;
use App\Http\Controllers\Pages\PortfolioPageController;
use App\Http\Controllers\Pages\PricingPageController;
use App\Http\Controllers\Pages\ServiceDetailPageController;
use App\Http\Controllers\Pages\ServicePageController;
use App\Http\Controllers\Pages\TeamMemberPageController;
use App\Http\Controllers\Pages\TeamPageController;
use App\Http\Controllers\Pages\TestimonialPageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\SeoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', HomePageController::class)->name('home');

Route::get('/about', AboutPageController::class)->name('about');

Route::get('/services', ServicePageController::class)->name('services');

Route::get('/contact', ContactPageController::class)->name('contact');
// The form saves the message, then the page opens WhatsApp. At most 5 per minute per visitor.
Route::post('/contact', ContactMessageController::class)->middleware('throttle:5,1')->name('contact.send');

Route::get('/portfolio', [PortfolioPageController::class, 'index'])->name('portfolio');
Route::get('/portfolio-experiment', [PortfolioPageController::class, 'experiment'])->name('portfolio.experiment');

Route::get('/pricing', PricingPageController::class)->name('pricing');

Route::get('/faqs', FaqPageController::class)->name('faqs');

Route::get('/team', TeamPageController::class)->name('team');

Route::get('/testimonials', TestimonialPageController::class)->name('testimonials');

Route::prefix('pages')->name('pages.')->group(function () {
    Route::get('/service/{slug}', ServiceDetailPageController::class)->name('service.single');

    Route::get('/team/{slug}', TeamMemberPageController::class)->name('team.single');
});

Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('robots');

Route::fallback(NotFoundPageController::class);




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
        // Profile and password
        Route::get('/profile-detail', [ProfileController::class, 'show'])->name('profile-detail');
        Route::get('/reset-password', [ProfileController::class, 'editPassword'])->name('reset-password');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('password.update.dashboard');
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
                'counters' => CounterController::class,
                'team-members' => TeamMemberController::class,
                'team-member-skills' => TeamMemberSkillController::class,
                'team-member-expertises' => TeamMemberExpertiseController::class,
                'services' => ServiceController::class,
                'service-benefits' => ServiceBenefitController::class,
                'service-faqs' => ServiceFaqController::class,
                'projects' => ProjectController::class,
                'pricing-plans' => PricingPlanController::class,
                'pricing-features' => PricingFeatureController::class,
                'page-list-items' => PageListItemController::class,
            ];
            foreach ($lists as $uri => $controller) {
                Route::get("/{$uri}", [$controller, 'index'])->name("{$uri}.index");
                Route::post("/{$uri}", [$controller, 'store'])->name("{$uri}.store");
                Route::patch("/{$uri}/{id}", [$controller, 'update'])->whereNumber('id')->name("{$uri}.update");
                Route::delete("/{$uri}/{id}", [$controller, 'destroy'])->whereNumber('id')->name("{$uri}.destroy");
                Route::post("/{$uri}/{id}/move", [$controller, 'move'])->whereNumber('id')->name("{$uri}.move");
            }
        });

        Route::get('/dashboard', DashboardController::class)->name('dashboard');

        // Inbox for the Contact page form.
        Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
        Route::patch('/messages/{id}', [MessageController::class, 'update'])->whereNumber('id')->name('messages.update');
        Route::delete('/messages/{id}', [MessageController::class, 'destroy'])->whereNumber('id')->name('messages.destroy');

        Route::prefix('pages')->name('pages.')->group(function () {
            Route::get('/analytics', AnalyticsController::class)->name('analytics');
        });
    });
});
