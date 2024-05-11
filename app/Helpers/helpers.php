<?php

namespace App\Helpers;

use App\Models\SubscribedPlan;

if (!function_exists('currentPlan')) {

    function currentPlan(): SubscribedPlan|null
    {
        $user = auth()->user();
        $tenant = $user->tenant;
        $subs = $tenant?->subscribedPlans()->where('status', 'active')?->first();

        return $subs;
    }
}

if (!function_exists('frequentUsers')) {


}
