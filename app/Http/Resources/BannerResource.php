<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class BannerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'desc' => $this->description,
            'image' => $this->image,
            'image_url' => $this->getImageUrl(),
            'base_url' => env('APP_URL').'/storage/',
        ];
    }

    protected function getImageUrl()
    {
        if (!$this->image) {
            return null;
        }

        if (Storage::disk('public')->exists($this->image)) {
            return asset('storage/' . $this->image);
        }

        return null;
    }
}