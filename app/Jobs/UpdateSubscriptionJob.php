<?php

namespace App\Jobs;

use App\Models\SubscribedPlan;
use App\Models\Tenant;
use App\Notifications\SubscriptionExpiredNotification;
use App\Notifications\SubscriptionReminderNotification;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateSubscriptionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        $sevenDaysAgo = Carbon::today()->subDays(7);
        $threeDaysAgo = Carbon::today()->subDays(3);
        $oneDayAgo = Carbon::today()->subDay();

        SubscribedPlan::where('status', 'active')
        ->each(function ($plan) use ($sevenDaysAgo, $threeDaysAgo, $oneDayAgo) {
            $endDate = Carbon::parse($plan->end_date);

            if ($endDate->isSameDay($sevenDaysAgo)) {
                $plan->tenant->user->notify(new SubscriptionReminderNotification($sevenDaysAgo));
            }

            if ($endDate->isSameDay($threeDaysAgo)) {
                $plan->tenant->user->notify(new SubscriptionReminderNotification($threeDaysAgo));
            }

            if ($endDate->isSameDay($oneDayAgo)) {
                $plan->tenant->user->notify(new SubscriptionReminderNotification($oneDayAgo));
            }

            // If the subscription has expired (end date is before today)
            if ($endDate->isBefore(Carbon::now())) {
                $plan->status = 'expired';
                $plan->save();
                $plan->tenant->user->notify(new SubscriptionExpiredNotification());
            }
        });


        Tenant::whereHas('subscribedPlans')
        ->latest()
        ->get()
        ->each(function ($tenant) {

            if($tenant->subscribedPlans->where('status', 'active')->exists()){
                return ;
            }
            // Find the first inactive plan for the current tenant
            $newSubscription = SubscribedPlan::where('status', 'inactive')
                ->where('tenant_id', $tenant->id)
                ->latest()
                ->first();

            // If an inactive plan is found, update its status to active
            if ($newSubscription && Carbon::parse($newSubscription->end_date)->isFuture()) {
                $newSubscription->update(['status' => 'active']);
            }
        });




    }
}