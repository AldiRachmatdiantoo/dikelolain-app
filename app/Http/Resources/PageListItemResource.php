<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\PageListItem
 */
class PageListItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'list_key' => $this->list_key,
            'text' => $this->text,
            'icon' => $this->icon,
            'icon_url' => $this->icon_url,
            'sort_order' => $this->sort_order,
            'is_active' => $this->is_active,
        ];
    }
}
