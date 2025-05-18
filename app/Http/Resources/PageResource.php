<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Helpers\ImageHelper;

class PageResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->title ?? '',
            'description' => $this->description ?? '',
            'image_url' => getFileFullUrl($this->image),
            'sections' => $this->sections ? $this->sections->map(function ($section) {
                return [
                    'type' => $section->type,
                    'title' => $section->title,
                    'description' => $section->description,
                    'image_url' => getFileFullUrl($section->image),
                ];
            }) : [],
        ];
    }
}
