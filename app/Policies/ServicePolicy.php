<?php

namespace App\Policies;

use App\Models\Service;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ServicePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->isVendor();
    }

    public function view(User $user, Service $service): bool
    {
        return $user->id === $service->vendor_id;
    }

    public function create(User $user): bool
    {
        return $user->isVendor();
    }

    public function update(User $user, Service $service): bool
    {
        return $user->id === $service->vendor_id;
    }

    public function delete(User $user, Service $service): bool
    {
        return $user->id === $service->vendor_id && !$service->hasActiveBookings();
    }

    public function toggleStatus(User $user, Service $service): bool
    {
        return $user->id === $service->vendor_id;
    }
} 