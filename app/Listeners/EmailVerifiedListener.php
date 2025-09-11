<?php

namespace App\Listeners;

use App\Enums\Role;
use App\Events\EmailVerified;

class EmailVerifiedListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(EmailVerified $event): void
    {
        $user = $event->user;
       //dd(Role::ORGANIZATION_OWNER->value);
        $user->assignRole(Role::ORGANIZATION_OWNER->value);

        !$user->hasVerifiedEmail() && $user->markEmailAsVerified();
    }
}
