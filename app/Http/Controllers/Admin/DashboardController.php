<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Resources\DashboardDataResource;
use App\Http\Resources\UserResource;
use App\Models\LoginLog;
use App\Models\SubscribedPlan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Batch;
use App\Models\Expense;
use App\Models\Farm;
use App\Models\Harvest;
use App\Models\HarvestCustomer;
use App\Models\Pond;
use App\Models\Purchase;
use App\Models\SubscriptionPlan;
use App\Models\Task;
use App\Models\Tenant;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    //

    public function __invoke(Request $request): JsonResponse
    {

        /** @var User $user */
        $users = User::query()
            ->whereRelation('roles', 'name', Role::ORGANIZATION_OWNER->value)
            ->with(['loginLogs:id,user_id,login_at,logout_at'])
            ->when($request->has('duration') && $request->input('duration') !== null, function (Builder $query) use ($request) {
                $lastSeen = match ($request->input('duration')) {
                    'today' => ['start_date' => Carbon::now()->startOfDay(), 'end_date' => now()->endOfDay()],
                    'yesterday' => ['start_date' => Carbon::now()->subDay()->startOfDay(), 'end_date' => now()->endOfDay()],
                    'one_week' => ['start_date' => Carbon::now()->subWeek()->addDay()->startOfDay(), 'end_date' => now()->endOfDay()],
                    'thirty_days' => ['start_date' => now()->subDays(30)->addDay()->startOfDay(), 'end_date' => now()->endOfDay()],
                    default => ['start_date' => Carbon::parse(explode(':', $request->query('duration'))[0]), 'end_date' => Carbon::parse(explode(':', $request->query('duration'))[1])]
                };

                return $query->whereHas('loginLogs', function (Builder $query) use ($lastSeen) {
                    return $query->whereBetween('login_at', [$lastSeen['start_date'], $lastSeen['end_date']]);
                });
            })
            ->when($request->query('search'), function (Builder $query) use ($request) {
                return $query->where('first_name', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('last_name', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('email', 'LIKE', '%' . $request->search . '%');
            })

            ->when($request->query('filter_by'), function (Builder $query) use ($request) {
                return match ($request->input('filter_by')) {
                    'free' => $query->whereHas('tenant', function (Builder $query) {
                        return $query->whereHas('subscribedPlans', function (Builder $query) {
                            return $query->where('type', 'free');
                        });
                    }),
                    default => $query->whereHas('tenant', function (Builder $query) {
                        return $query->whereHas('subscribedPlans', function (Builder $query) {
                            return $query->where('type', 'paid');
                        });
                    }),
                };
            })
            ->latest()

            ->paginate($request->per_page ?? 10);


        $lastSeen = match ($request->input('duration')) {
            'today' => ['start_date' => Carbon::now()->startOfDay(), 'end_date' => now()->endOfDay()],
            'yesterday' => ['start_date' => Carbon::now()->subDay()->startOfDay(), 'end_date' => now()->endOfDay()],
            'one_week' => ['start_date' => Carbon::now()->subWeek()->addDay()->startOfDay(), 'end_date' => now()->endOfDay()],
            'thirty_days' => ['start_date' => now()->subDays(30)->addDay()->startOfDay(), 'end_date' => now()->endOfDay()],
            default => ['start_date' => Carbon::now()->startOfDay(), 'end_date' => now()->endOfDay()]
        };



        $cardDetails = [
            'total_signups' => User::query()
                ->whereRelation('roles', 'name', Role::ORGANIZATION_OWNER->value)
                ->whereBetween('created_at', [$lastSeen['start_date'], $lastSeen['end_date']])->count(),
            'paid_users' => SubscribedPlan::where('type', 'paid')
                ->whereBetween('created_at', [$lastSeen['start_date'], $lastSeen['end_date']])->count(),
            'free_users' => SubscribedPlan::where('type', 'free')
                ->whereBetween('created_at', [$lastSeen['start_date'], $lastSeen['end_date']])->count(),
            'active_users' => $this->frequentUsers($lastSeen['start_date'], $lastSeen['end_date'])['active_users'],
            'inactive_users' => $this->frequentUsers($lastSeen['start_date'], $lastSeen['end_date'])['inactive_users'],

        ];


        $data = [
            'card_data' => $cardDetails,
            'users' => DashboardDataResource::collection($users)->response()->getData()
        ];

        return $this->success(
            message: "dashboard data retrived",
            data: $data,
            code: 200
        );
    }


    private function frequentUsers(mixed $start_date, mixed $end_date): array
    {

        $models = [
            Harvest::class,
            HarvestCustomer::class,
            Purchase::class,
            Batch::class,
            // Farm::class,
            Expense::class,
            Task::class,
            Pond::class,
        ];

        $tenantCounts = [];
        $tenants = [];

        foreach ($models as $model) {
            $farmsId = $model::whereBetween('created_at', [$start_date, $end_date])->pluck('farm_id')->unique();
            foreach ($farmsId as $farmId) {
                $userId = Farm::find($farmId)->tenant?->user?->id;
                if (!isset($tenantCounts[$userId])) {
                    $tenantCounts[] = 1;
                    $tenants[] = $userId;
                }
            }
        }

        $activeUsers = array_sum($tenantCounts);
        $inactiveUsers = User::query()
            ->whereRelation('roles', 'name', Role::ORGANIZATION_OWNER->value)
            ->whereNotIn('id', $tenants)->count();


        return [
            'active_users' => $activeUsers,
            'inactive_users' => $inactiveUsers
        ];
    }
}
