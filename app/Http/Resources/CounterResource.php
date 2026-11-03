<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Counter
 */
class CounterResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'number' => $this->number,
            'suffix' => $this->suffix,
            'label' => $this->label,
            'sort_order' => $this->sort_order,
            'is_active' => $this->is_active,
        ];
    }
}
