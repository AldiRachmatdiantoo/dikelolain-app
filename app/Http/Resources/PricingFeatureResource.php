<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\PricingFeature
 */
class PricingFeatureResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'pricing_plan_id' => $this->pricing_plan_id,
            'text' => $this->text,
            'sort_order' => $this->sort_order,
            'is_active' => $this->is_active,
        ];
    }
}
