<?php

namespace App\Services\Pages;

use App\Services\PricingPlanService;
use App\Vcms\Fields\PricingFeatureFields;
use App\Vcms\Fields\PricingPlanFields;

class PricingPageService
{
    public function __construct(
        private SectionData $sections,
        private PricingPlanService $plans,
    ) {
    }

    /**
     * @param  bool  $editing  also list hidden items, and add the editor field definitions
     */
    public function data(bool $editing = false): array
    {
        return [
            ...$this->sections->legacy(),
            ...$this->sections->settings(),
            ...$this->sections->tools($editing),
            'plans' => $this->plans->withFeatures(activeOnly: !$editing),
            'planFields' => $editing ? PricingPlanFields::definition() : [],
            'featureFields' => $editing ? PricingFeatureFields::definition() : [],
        ];
    }
}
