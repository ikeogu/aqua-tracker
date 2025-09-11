<?php

namespace App\Jobs;

use App\Models\PaymentInfo;
use App\Models\SubscribedPlan;
use App\Models\Tenant;
use App\Notifications\SubscriptionExpiredNotification;
use App\Notifications\SubscriptionReminderNotification;
use App\Services\PaymentService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

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
    public function handle(PaymentService $paymentService): void
    {
        //
        $reminderDays = [
            7 => Carbon::today()->subDays(7),
            3 => Carbon::today()->subDays(3),
            1 => Carbon::today()->subDay(),
        ];

        SubscribedPlan::where('status', 'active')
            ->with(['tenant.user']) // eager load to avoid N+1
            ->each(function (SubscribedPlan $plan) use ($reminderDays, $paymentService) {
                $endDate = Carbon::parse($plan->end_date);
                $today   = Carbon::now();

                // 🔔 Send reminders
                foreach ($reminderDays as $days => $date) {
                    if ($endDate->isSameDay($date)) {
                        $plan->tenant->user->notify(
                            new SubscriptionReminderNotification($date)
                        );
                    }
                }

                Log::debug([
                    'end_date' => $endDate->toDateString(),
                    'today'    => $today->toDateString(),
                ]);

                // ⛔ Handle expired subscriptions
                if ($endDate->isBefore($today)) {
                    $paymentInfo = PaymentInfo::query()
                        ->where('tenant_id', $plan->tenant_id)
                        ->where('auto_renewal', true)
                        ->first();

                    if ($paymentInfo) {
                        $paymentService->autoRenew($paymentInfo->tenant);
                    } else {
                        $plan->update(['status' => 'expired']);
                        $plan->tenant->user->notify(new SubscriptionExpiredNotification());
                    }
                }
            });


        Tenant::whereHas('subscribedPlans')
            ->latest()
            ->get()
            ->each(function ($tenant) {

                // Check if the tenant already has an active subscription
                if ($tenant->subscribedPlans->where('status', 'active')->isNotEmpty()) {
                    return; // Skip to the next tenant
                }

                // Find the latest inactive plan for the current tenant that is still valid
                $newSubscription = SubscribedPlan::where('status', 'inactive')
                    ->where('tenant_id', $tenant->id)
                    ->first();

                // If an inactive plan is found and it has not expired, update its status to active
                if ($newSubscription && Carbon::parse($newSubscription->end_date)->isFuture()) {
                    $newSubscription->update(['status' => 'active']);
                }
            });
    }
}
