<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //

    public function __invoke(Request $request) : JsonResponse
    {

       $users = User::query()
        ->whereRelation('roles', 'name', Role::FARM_TEAM_OWNER->value)
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
            return match ($request->input('sort_by')) {
                'free' => $query->orderBy('name', 'asc'),
                'paid' => $query->orderBy('name', 'desc')
            };
        })

        ->paginate($request->per_page ?? 10)
        ->through(function($item){
            return [
                'name' => $item->first_name . ' '. $item->last_name,
                'email' => $item->email,
                "subscription_plan" => 'free',
                'created_at' => $item->created_at,
                'last_seen' => $item->loginLogs()->latest()->first()->login_at,
                'status' => 'active'
            ];
        });


        $lastSeen = match ($request->input('duration')) {
            'today' => ['start_date' => Carbon::now()->startOfDay(), 'end_date' => now()->endOfDay()],
            'yesterday' => ['start_date' => Carbon::now()->subDay()->startOfDay(), 'end_date' => now()->endOfDay()],
            'one_week' => ['start_date' => Carbon::now()->subWeek()->addDay()->startOfDay(), 'end_date' => now()->endOfDay()],
            'thirty_days' => ['start_date' => now()->subDays(30)->addDay()->startOfDay(), 'end_date' => now()->endOfDay()],
            default => ['start_date' => Carbon::parse(explode(':', $request->query('duration'))[0]), 'end_date' => Carbon::parse(explode(':', $request->query('duration'))[1])]
        };


       $cardDetails = [
            'total_signups' => User::query()
                ->whereRelation('roles', 'name', Role::FARM_TEAM_OWNER->value)
                ->whereBetween('created_at', [$lastSeen['start_date'], $lastSeen['end_date']])->count(),
            'paid_users' => 0,
            'free_users' => 0,
            'active_users' => 50,
            'inactive_users' => 0,

       ];



       $data = [
            'card_data' => $cardDetails,
            'users' => $users
       ];

       return $this->success(
            message:"dashboard data retrived",
            data: $data,
            code: 200
       );

    }
}
