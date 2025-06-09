<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Branch;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('branch.{branchId}', function ($user, $branchId) {
    $branch = Branch::find($branchId);
    return $branch && $user->id === $branch->vendor_id;
});

// User-specific notifications channel
Broadcast::channel('notifications.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

// Vendor-specific channels
Broadcast::channel('vendor.{vendorId}', function ($user, $vendorId) {
    // Check if user is a vendor and the vendorId matches their user ID
    return $user->isVendor() && (int) $user->id === (int) $vendorId;
});

// Vendor notifications channel
Broadcast::channel('vendor.notifications.{vendorId}', function ($user, $vendorId) {
    // Check if user is a vendor and the vendorId matches their user ID
    return $user->isVendor() && (int) $user->id === (int) $vendorId;
});

// General user channel (for all authenticated users)
Broadcast::channel('user.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});