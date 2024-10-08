<?php

namespace App\Jobs;

use App\Models\SubscribedPlan;
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
        $sevenDaysAgo = Carbon::now()->subDays(7);
        $threeDaysAgo = Carbon::now()->subDays(3);
        $oneDayAgo = Carbon::now()->subDay();

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




    }
}