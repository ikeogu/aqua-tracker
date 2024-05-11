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
            ->each(function ($query) use ($sevenDaysAgo, $threeDaysAgo, $oneDayAgo) {
                if(Carbon::parse($query->end_date)->equalTo($sevenDaysAgo)){
                    $query->tenant->user->notify(new SubscriptionReminderNotification($sevenDaysAgo));
                }
                if(Carbon::parse($query->end_date)->equalTo($threeDaysAgo)){
                    $query->tenant->user->notify(new SubscriptionReminderNotification($threeDaysAgo));
                }
                if(Carbon::parse($query->end_date)->equalTo($oneDayAgo)){
                    $query->tenant->user->notify(new SubscriptionReminderNotification($oneDayAgo));
                }

                if(Carbon::parse($query->end_date)->lessThan($oneDayAgo)){
                    $query->tenant->user->notify(new SubscriptionExpiredNotification());
                }
            });



    }
}
