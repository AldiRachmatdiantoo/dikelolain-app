<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Project
 */
class ProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'media_type' => $this->media_type,
            'video_url' => $this->video_url,
            'embed_url' => $this->embed_url,
            'image' => $this->image,
            'image_url' => $this->image_url,
            'link' => $this->link,
            'sort_order' => $this->sort_order,
            'is_active' => $this->is_active,
        ];
    }
}
