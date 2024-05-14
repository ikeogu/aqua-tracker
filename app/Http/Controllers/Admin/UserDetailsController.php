<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Expense;
use App\Models\Harvest;
use App\Models\Inventory;
use App\Models\Pond;
use App\Models\Purchase;
use App\Models\Tenant;
use App\Models\User;
use App\Notifications\DeactivateActivateNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use function App\Helpers\currentPlan;

class UserDetailsController extends Controller
{
    //

    public function __invoke(Request $request, Tenant $tenant) : JsonResponse
    {

        /** @var User $user */
        $user = $tenant->owner();

        $currentPlan = currentPlan();
        $details = [
            'first_name' => $user?->first_name,
            'last_name' => $user?->last_name,
            'email' => $user?->email,
            'organization_name' => $user?->tenant?->title,
            'subscription_plan' => $currentPlan?->subscriptionPlan?->title,
            'subscription_status' =>$currentPlan?->status,
            'phone_number' => $user?->phone_number,
            'status' => $user?->tenant?->status,
            'tenant_id' => $user?->tenant?->id
        ];

        $farmsId = $user?->tenant?->farms()->get()->pluck('id');

        $overview = [
            'capital' => Inventory::whereIn('farm_id', $farmsId)->sum('amount') + Batch::whereIn('farm_id', $farmsId)->sum('amount_spent'),
            'net_profit' => Purchase::whereIn('farm_id', $farmsId)->sum('amount') - Inventory::whereIn('farm_id', $farmsId)->sum('amount')
                 - Batch::whereIn('farm_id', $farmsId)->sum('amount_spent'),
            'total_expense' => Expense::whereIn('farm_id', $farmsId)->sum('total_amount'),

        ];

        $farmsId = $farmsId ?? [];

        // Retrieve counts using the updated $farmsId
        $farmStatistics = [
            'total_batches' => Batch::whereIn('farm_id', $farmsId)->count(),
            'total_harvests' => Harvest::whereIn('farm_id', $farmsId)->count(),
            'total_farms' => $user->tenant->farms()->count(),
            'total_ponds' => Pond::whereIn('farm_id', $farmsId)->count()
        ];

        // If any count returns null (due to an empty or null $farmsId), set it to 0
        foreach ($farmStatistics as &$statistic) {
            $statistic = $statistic ?? 0;
        }

        $data = [
            'overview' => $overview,
            'farm_statistics' => $farmStatistics,
            'details' => $details
        ];


        return $this->success(
            message:"dashboard data retrived",
            data: $data,
            code: 200
       );
    }

    public function accountActivation(Request $request, Tenant $tenant) : JsonResponse
    {
        $request->validate([
            'deactive_activate' => ['required', 'boolean']
        ]);


        match($request->deactive_activate) {
            true => $this->deactive($tenant),
            false => $this->activate($tenant)
        };


        $msg = ($request->deactive_activate) ? "Account was deactivated successfully" : "Account was activated successfully";

        return $this->success(
            message:$msg,
            code: 200
       );
    }

    private function deactive(Tenant $tenant) : void
    {
        $tenant->update(['status' => Status::INACTIVE->value]);

        $tenant->owner()->notify(new DeactivateActivateNotification(true));
        $tenant->owner()->delete();

    }

    private function activate(Tenant $tenant) : void
    {
        $tenant->update(['status' => Status::ACTIVE->value]);
        $tenant->owner()->notify(new DeactivateActivateNotification(false));
        $tenant->owner()->restore();
    }
}
