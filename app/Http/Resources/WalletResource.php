<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WalletResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'balance' => $this->balance,
            'pending_balance' => $this->pending_balance,
            'total_earned' => $this->total_earned,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
} 