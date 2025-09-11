<?php

namespace App\Helpers;

use App\Models\SubscribedPlan;
use Illuminate\Support\Facades\Auth;

if (!function_exists('currentPlan')) {

    function currentPlan(): SubscribedPlan|null
    {
        $user = Auth::user();
        $tenant = $user->tenant;
        $subs = $tenant?->subscribedPlans()->where('status', 'active')?->first();

        return $subs;
    }
}

if (!function_exists('frequentUsers')) {


}
