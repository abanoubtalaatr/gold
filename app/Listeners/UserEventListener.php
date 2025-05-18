<?php

namespace App\Listeners;

use Carbon\Carbon;
use App\Events\User\UserLoggedIn;
use App\Events\User\ApiRegistered;
use Illuminate\Support\Facades\Mail;
use App\Events\User\UserStatusChanged;
//use App\Mail\AccountStatusNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;

/**
 * Class UserEventListener.
 */
class UserEventListener
{

    
    /**
     * @param $event
     */
    public function onLoggedIn($event)
    {
        //$guest_token = request()->header('Guest-Token');
        $user = $event->user;
        // Update the logging in users time & IP
        $user->update([
            'last_login_at' => Carbon::now(),
            // 'last_login_ip' => request()->getClientIp(),
        ]);


    }

    /**
     * @param $event
     */
    public function onPasswordReset($event) {}

    /**
     * @param $event
     */
    public function onCreated($event) {}

    /**
     * @param $event
     */
    public function onDeleted($event) {}

    /**
     * @param $event
     */
    public function onStatusChanged($event)
    {

        $user = $event->user;

       // Mail::to($user->email)->send(new AccountStatusNotification($user, $event->status));
    }

    /**
     * @param $event
     */
    public function onApiRegistered($event)
    {
        $user = $event->user;
        if ( null !== $user->email  && $user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();
        }
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param \Illuminate\Events\Dispatcher $events
     */
    public function subscribe($events)
    {
        $events->listen(
            UserLoggedIn::class,
            'App\Listeners\UserEventListener@onLoggedIn'
        );

        $events->listen(
            UserStatusChanged::class,
            'App\Listeners\UserEventListener@onStatusChanged'
        );

        $events->listen(
            ApiRegistered::class,
            'App\Listeners\UserEventListener@onApiRegistered'
        );
    }
}
