<?php

namespace App\Http\Controllers;

use App\Enums\HttpStatusCode;
use App\Enums\Role;
use App\Http\Requests\UpdateOrganazationRequest;
use App\Models\Batch;
use App\Models\Expense;
use App\Models\Farm;
use App\Models\Harvest;
use App\Models\Pond;
use App\Models\SubscribedPlan;
use App\Models\Tenant;
use App\Models\TenantUser;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class OrganizationController extends Controller
{
    //

    public function __invoke(UpdateOrganazationRequest $request, Tenant $tenant): JsonResponse

    {
        /** @var User $user */
        $user = Auth::user();
        if ($user->hasRole(Role::VIEW_FARMS->value)) {
            return $this->error(
                message: "Your current role does not permit this action, kindly contact the Admin.",
                code: HttpStatusCode::FORBIDDEN->value
            );
        }
        /** @var User $user */
        $user = Auth::user();

        if (!$user->hasRole(Role::ORGANIZATION_OWNER->value) || $user->tenant !== $tenant->id) {
            $this->error(
                message: "unathorized action.",
                code: 403
            );
        }

        $tenant->update($request->validated());

        return $this->success(
            message: 'Organization updated successfully',
            data: $tenant,
            code: HttpStatusCode::SUCCESSFUL->value
        );
    }

    public function checkSubscription(Farm $farm): JsonResponse
    {
        $response = SubscribedPlan::query()
            ->where('tenant_id', $farm->tenant->id)
            ->where('status', 'active')
            ->whereNull('deleted_at')
            ->exists();


        return $this->success(
            message: 'Organization subscription status',
            data: [
                'active_subscription' => $response
            ],
            code: HttpStatusCode::SUCCESSFUL->value
        );
    }

    public function destroy(array $userIds): JsonResponse
    {
        try {


            DB::transaction(function () use ($userIds) {
                User::whereIn('id', $userIds)->delete();
                // Step 2: Delete from tenant_user
                TenantUser::whereIn('user_id', $userIds)->delete();

                // Step 3: Identify tenant IDs from user IDs
                $tenantIds = Tenant::whereIn('user_id', $userIds)->pluck('id');

                // Step 4: Identify farm IDs from tenant IDs
                $farmIds = Farm::whereIn('tenant_id', $tenantIds)->pluck('id');

                // Step 5: Delete from farms
                Farm::whereIn('tenant_id', $tenantIds)->delete();

                // Step 6: Delete from related tables where farm_id matches
                Batch::whereIn('farm_id', $farmIds)->delete();
                Expense::whereIn('farm_id', $farmIds)->delete();
                Pond::whereIn('farm_id', $farmIds)->delete();
                Harvest::whereIn('farm_id', $farmIds)->delete();
                Tenant::whereIn('user_id', $userIds)->delete();
            });

            Log::info('Records successfully deleted based on the provided user IDs.');

            return $this->success(
                message: 'Organization/s deleted',
                code: HttpStatusCode::SUCCESSFUL->value
            );
        } catch (\Throwable $th) {
            //throw $th;
            Log::debug(['error' => $th]);
            return $this->success(
                message: 'Organization deletion failed',
                code: HttpStatusCode::SERVER_ERROR->value
            );
        }
    }
}
