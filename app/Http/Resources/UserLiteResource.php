<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserLiteResource extends JsonResource
{
    public function toArray($request)
    {
        $data =  [
            'id' => $this->id,
            'name' => $this->name ?? '',
            'email' => $this->email ?? '',
            'phone' => $this->phone ?? '',
            'mobile' => $this->mobile ?? '',
            'comerical_register' => $this->commercial_register ?? '',
            'type' => $this->type,
        ];

        return $data;
        }



        public function isProviderProfileComplete()
        {
            return $this->provider && $this->provider->company_name && $this->provider->registration_number && $this->provider->registration_country ;


        }


}