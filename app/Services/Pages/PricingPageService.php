<?php

namespace App\Services\Pages;

use App\Services\PageListItemService;
use App\Services\PricingPlanService;
use App\Vcms\Fields\PageListItemFields;
use App\Vcms\Fields\PricingFeatureFields;
use App\Vcms\Fields\PricingPlanFields;

class PricingPageService
{
    public function __construct(
        private SectionData $sections,
        private PricingPlanService $plans,
        private PageListItemService $listItems,
    ) {
    }

    /**
     * @param  bool  $editing  also list hidden items, and add the editor field definitions
     */
    public function data(bool $editing = false): array
    {
        return [
            ...$this->sections->settings(),
            ...$this->sections->tools($editing),
            'plans' => $this->plans->withFeatures(activeOnly: !$editing),
            'planFields' => $editing ? PricingPlanFields::definition() : [],
            'featureFields' => $editing ? PricingFeatureFields::definition() : [],
            'benefits' => $this->listItems->lists(['pricing_benefits'], activeOnly: !$editing)['pricing_benefits'],
            'benefitFields' => $editing ? PageListItemFields::definition(withIcon: true) : [],
        ];
    }
}
