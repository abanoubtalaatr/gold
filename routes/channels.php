<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Branch;

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

