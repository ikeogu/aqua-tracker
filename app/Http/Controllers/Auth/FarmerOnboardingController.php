<?php

namespace App\Http\Controllers\Auth;

use App\Actions\BootstrapFarmerAsTenant;
use App\Enums\HttpStatusCode;
use App\Http\Controllers\Controller;
use App\Http\Requests\OnboardFarmerRequest;
use Illuminate\Http\Request;
use App\Models\User;

class FarmerOnboardingController extends Controller
{
    //
    public function __invoke(OnboardFarmerRequest $request)
    {

         /** @var User $user */
         $user = $request->user();

         BootstrapFarmerAsTenant::execute($user, $request->validated());

         return $this->success(
             code: HttpStatusCode::SUCCESSFUL->value,
             message: 'Creator onboarding completed successfully',
         );
    }
}
