<?php

namespace App\Providers;

use App\Repositories\Contracts\ClientLogoRepositoryInterface;
use App\Repositories\Contracts\FaqRepositoryInterface;
use App\Repositories\Contracts\PageSettingRepositoryInterface;
use App\Repositories\Contracts\TestimonialRepositoryInterface;
use App\Repositories\Eloquent\EloquentClientLogoRepository;
use App\Repositories\Eloquent\EloquentFaqRepository;
use App\Repositories\Eloquent\EloquentPageSettingRepository;
use App\Repositories\Eloquent\EloquentTestimonialRepository;
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
    ];
}
