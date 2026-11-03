<?php

namespace App\Services\Pages;

use App\Services\ServiceService;
use App\Services\SettingService;
use App\Vcms\Fields\ServiceBenefitFields;
use App\Vcms\Fields\ServiceFaqFields;
use App\Vcms\Fields\ServiceFields;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ServiceDetailPageService
{
    public function __construct(
        private ServiceService $services,
        private SettingService $settings,
    ) {
    }

    /**
     * @param  bool  $editing  also show a hidden service and hidden benefits/FAQs, and add the editor field definitions
     *
     * @throws NotFoundHttpException for an unknown slug, or a hidden service outside edit mode
     */
    public function data(string $slug, bool $editing = false): array
    {
        $service = $this->services->findBySlug($slug, activeOnly: !$editing)
            ?? throw new NotFoundHttpException();

        return [
            'settings' => $this->settings,
            'service' => $service,
            // Sidebar list of services; the current one stays listed even when hidden.
            'allServices' => $this->services->all(activeOnly: true)->when(!$service->is_active, fn ($all) => $all->push($service)),
            'serviceFields' => $editing ? ServiceFields::definition() : [],
            'benefitFields' => $editing ? ServiceBenefitFields::definition() : [],
            'faqFields' => $editing ? ServiceFaqFields::definition() : [],
        ];
    }
}
