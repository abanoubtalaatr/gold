<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        //     if (!app()->runningInConsole()) { // Stop Observer  During DB Seeding

        // }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        // Get the original data before the update
        // $originalData = $user->getOriginal();


    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void {}

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
