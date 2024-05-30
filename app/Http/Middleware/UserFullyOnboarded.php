<?php

namespace App\Http\Middleware;

use App\Enums\HttpStatusCode;
use App\Traits\RespondsWithHttpStatus;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserFullyOnboarded
{
    use RespondsWithHttpStatus;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
         /** @var User $user */
         $user = $request->user();

         setPermissionsTeamId(0);


         if (!$user->fully_onboarded) {
            return $this->error(
                message: 'User is yet to onboard!',
                error: 'creator_onboarding',
                code: HttpStatusCode::FORBIDDEN->value
            );
        }

        setPermissionsTeamId($user->tenant ?? 0);
        return $next($request);
    }
}
