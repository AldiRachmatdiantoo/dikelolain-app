<?php

namespace App\Providers;

use App\Repositories\Contracts\AchievementRepositoryInterface;
use App\Repositories\Contracts\ClientLogoRepositoryInterface;
use App\Repositories\Contracts\CounterRepositoryInterface;
use App\Repositories\Contracts\FaqRepositoryInterface;
use App\Repositories\Contracts\PageSettingRepositoryInterface;
use App\Repositories\Contracts\PricingFeatureRepositoryInterface;
use App\Repositories\Contracts\PricingPlanRepositoryInterface;
use App\Repositories\Contracts\ProcessStepRepositoryInterface;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use App\Repositories\Contracts\ServiceBenefitRepositoryInterface;
use App\Repositories\Contracts\ServiceFaqRepositoryInterface;
use App\Repositories\Contracts\ServiceRepositoryInterface;
use App\Repositories\Contracts\TeamMemberExpertiseRepositoryInterface;
use App\Repositories\Contracts\TeamMemberRepositoryInterface;
use App\Repositories\Contracts\TeamMemberSkillRepositoryInterface;
use App\Repositories\Contracts\TestimonialRepositoryInterface;
use App\Repositories\Contracts\ToolRepositoryInterface;
use App\Repositories\Eloquent\EloquentAchievementRepository;
use App\Repositories\Eloquent\EloquentClientLogoRepository;
use App\Repositories\Eloquent\EloquentCounterRepository;
use App\Repositories\Eloquent\EloquentFaqRepository;
use App\Repositories\Eloquent\EloquentPageSettingRepository;
use App\Repositories\Eloquent\EloquentPricingFeatureRepository;
use App\Repositories\Eloquent\EloquentPricingPlanRepository;
use App\Repositories\Eloquent\EloquentProcessStepRepository;
use App\Repositories\Eloquent\EloquentProjectRepository;
use App\Repositories\Eloquent\EloquentServiceBenefitRepository;
use App\Repositories\Eloquent\EloquentServiceFaqRepository;
use App\Repositories\Eloquent\EloquentServiceRepository;
use App\Repositories\Eloquent\EloquentTeamMemberExpertiseRepository;
use App\Repositories\Eloquent\EloquentTeamMemberRepository;
use App\Repositories\Eloquent\EloquentTeamMemberSkillRepository;
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
        ProjectRepositoryInterface::class => EloquentProjectRepository::class,
        PricingPlanRepositoryInterface::class => EloquentPricingPlanRepository::class,
        PricingFeatureRepositoryInterface::class => EloquentPricingFeatureRepository::class,
        AchievementRepositoryInterface::class => EloquentAchievementRepository::class,
        CounterRepositoryInterface::class => EloquentCounterRepository::class,
        TeamMemberRepositoryInterface::class => EloquentTeamMemberRepository::class,
        TeamMemberSkillRepositoryInterface::class => EloquentTeamMemberSkillRepository::class,
        TeamMemberExpertiseRepositoryInterface::class => EloquentTeamMemberExpertiseRepository::class,
        ServiceRepositoryInterface::class => EloquentServiceRepository::class,
        ServiceBenefitRepositoryInterface::class => EloquentServiceBenefitRepository::class,
        ServiceFaqRepositoryInterface::class => EloquentServiceFaqRepository::class,
    ];
}
