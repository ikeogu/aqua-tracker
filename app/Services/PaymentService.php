<?php

namespace App\Services;

use App\Http\Clients\PaystackClient;
use App\Models\PaymentInfo;
use App\Models\SubscribedPlan;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Models\User;
use App\Notifications\PaymentInfoNotification;
use Illuminate\Http\Request;

class PaymentService
{
    public function __construct(
        public PaystackClient $paystackClient
    )
    {

    }
    public function addFreePlanToTenant(Tenant $tenant) : void
    {

        $subscriptionPlan = SubscriptionPlan::where('type', 'free')->latest()->first();

        $newStartsAt =  now();
        $newExpiresAt = $newStartsAt->addMonths($subscriptionPlan->duration);
       $subscribedPlan = SubscribedPlan::create([
            'subscription_plan_id' => $subscriptionPlan->id,
            'status' => 'active',
            'no_of_months' => $subscriptionPlan->duration,
            'amount' => 0,
            'reference' => 'free',
            'start_date' => $newStartsAt,
            'end_date' => $newExpiresAt,
            'payment_method' => 'N/A',
            'type' =>'free',
            "tenant_id" => $tenant->id
        ]);

        $tenant->user->notify(new PaymentInfoNotification($subscribedPlan));
    }

    public function upgradeUserPlan(Request $request, User $user) : array
    {
        $subscriptionPlan = SubscriptionPlan::where('type', 'paid')->latest()->first();

        $existingPlan = SubscribedPlan::where('tenant_id', $user->tenant->id)->latest()->first();

        $newStartsAt = ($existingPlan?->expires_at) ? $existingPlan?->expires_at : now();


        $newExpiresAt = $newStartsAt->addMonths($request->no_of_months);

        $subscribedPlan = SubscribedPlan::create([
            'subscription_plan_id' => $subscriptionPlan->id,
            'tenant_id' => $user->tenant->id,
            'no_of_months' =>  $request->no_of_months,
            'reference' => uniqid(),
            'status' => 'pending',
            'amount' => $subscriptionPlan->applyDiscount($request->no_of_months),
            'start_date' => $newStartsAt,
            'end_date' => $newExpiresAt,
            'payment_method' => 'N/A',
            'type' =>'paid'

        ]);

        $data = [
            'email' => $user->email,
            'amount' => $subscribedPlan->amount * 100,
            'reference' => $subscribedPlan->reference,
            //'callback_url' => route('verifyPayment')
        ];

       // return $this->paystackClient->initiateTransaction($data);
        return $data;


    }

    public function autoRenew(Tenant $tenant) : bool
    {
        $paymentInfo = PaymentInfo::where('tenant_id', $tenant->id)->latest()->first();
        $token = json_decode($paymentInfo['authorization'])['authorization_code'];
        $subscribedPlan = SubscribedPlan::where('tenant_id', $tenant->id)->latest()->first();

        $response = $this->paystackClient->chargeCard($token, $subscribedPlan->amount, $tenant->user->email);

        if(!$response['status']){
            return false;
        }

       $subscribedPlan = SubscribedPlan::create([

            'subscription_plan_id' => $subscribedPlan->subscription_plan_id,
            'tenant_id' => $tenant->id,
            'no_of_months' => $subscribedPlan->no_of_months,
            'reference' => $response['reference'],
            'status' => 'active',
            'amount' => $subscribedPlan->amount,
            'start_date' => now(),
            'end_date' => now()->addMonths($subscribedPlan->no_of_months),
            'payment_method' => $response['channel'],
            'type' =>'paid'
        ]);

        $tenant->user->notify(new PaymentInfoNotification($subscribedPlan));

    }

}
