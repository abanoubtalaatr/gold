<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class NotificationResource extends JsonResource
{
    public function toArray($request)
    {
        $date = Carbon::parse($this->created_at);
        $lang = request()->header('Accept-Language', 'ar') ?? 'ar';

        $title = isset($this->data['title'][$lang])
            ? $this->data['title'][$lang]
            : ($this->title ?? $this->data['title'] ?? '');

        $message = isset($this->data['message'][$lang])
            ? $this->data['message'][$lang]
            : ($this->message ?? $this->data['message'] ?? '');

        return [
            'id' => $this->id,
            'title' =>  $title,
            'message' =>  $message,
            'type' => $this->data['type'] ?? $this->type,
            'is_read' => !is_null($this->read_at),
            'created_at' => $date->format('d M,Y'),
        ];
    }
}