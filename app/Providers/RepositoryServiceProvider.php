<?php

namespace App\Providers;

use App\Repositories\Contracts\AchievementRepositoryInterface;
use App\Repositories\Contracts\ClientLogoRepositoryInterface;
use App\Repositories\Contracts\FaqRepositoryInterface;
use App\Repositories\Contracts\PageSettingRepositoryInterface;
use App\Repositories\Contracts\ProcessStepRepositoryInterface;
use App\Repositories\Contracts\TestimonialRepositoryInterface;
use App\Repositories\Contracts\ToolRepositoryInterface;
use App\Repositories\Eloquent\EloquentAchievementRepository;
use App\Repositories\Eloquent\EloquentClientLogoRepository;
use App\Repositories\Eloquent\EloquentFaqRepository;
use App\Repositories\Eloquent\EloquentPageSettingRepository;
use App\Repositories\Eloquent\EloquentProcessStepRepository;
use App\Repositories\Eloquent\EloquentTestimonialRepository;
use App\Repositories\Eloquent\EloquentToolRepository;
use Illuminate\Support\ServiceProvider;

/**
 * Binds every repository interface to its implementation (ARCHITECTURE.md §2).
 */
class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * @var array<class-string, class-string>
     */
    public array $bindings = [
        PageSettingRepositoryInterface::class => EloquentPageSettingRepository::class,
        FaqRepositoryInterface::class => EloquentFaqRepository::class,
        TestimonialRepositoryInterface::class => EloquentTestimonialRepository::class,
        ClientLogoRepositoryInterface::class => EloquentClientLogoRepository::class,
        ToolRepositoryInterface::class => EloquentToolRepository::class,
        ProcessStepRepositoryInterface::class => EloquentProcessStepRepository::class,
        AchievementRepositoryInterface::class => EloquentAchievementRepository::class,
    ];
}
