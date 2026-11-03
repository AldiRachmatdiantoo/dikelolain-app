<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Service
 */
class ServiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'summary' => $this->summary,
            'image' => $this->image,
            'image_url' => $this->image_url,
            'icon' => $this->icon,
            'icon_url' => $this->icon_url,
            'feature_image' => $this->feature_image,
            'feature_image_url' => $this->feature_image_url,
            'description' => $this->description,
            'benefits_title' => $this->benefits_title,
            'benefits_intro' => $this->benefits_intro,
            'sort_order' => $this->sort_order,
            'is_active' => $this->is_active,
        ];
    }
}
