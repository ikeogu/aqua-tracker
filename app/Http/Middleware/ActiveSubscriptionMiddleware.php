<?php

namespace App\Http\Middleware;

use App\Enums\HttpStatusCode;
use App\Models\SubscribedPlan;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class ActiveSubscriptionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var User $user */
        $user = $request->user();

        if (!SubscribedPlan::where('tenant_id', $user->tenant->id)->where('status', 'active')->exists()) {
            abort(403, 'Your subscription is not active. Kindly upgrade to continue.');
        }

        return $next($request);
    }
}
