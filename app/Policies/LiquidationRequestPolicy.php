<?php

namespace App\Policies;

use App\Models\LiquidationRequest;
use App\Models\User;

class LiquidationRequestPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, LiquidationRequest $liquidationRequest): bool
    {
        return $user->id === $liquidationRequest->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, LiquidationRequest $liquidationRequest): bool
    {
        return $user->id === $liquidationRequest->user_id && $liquidationRequest->status === 'pending';
    }
} 