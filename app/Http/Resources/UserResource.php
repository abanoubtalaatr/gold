<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ProviderProfileResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'type' => $this->type,
            'avatar' => $this->avatar,
            'is_phone_verified' => $this->is_phone_verified,
        ];
        }

        public function isProviderProfileComplete()
        {
            return $this->provider && $this->provider->company_name && $this->provider->registration_number && $this->provider->registration_country ;


        }

}