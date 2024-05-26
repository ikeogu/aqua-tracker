<?php

namespace App\Http\Controllers\Subscription;

use App\Enums\HttpStatusCode;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateSubscriptionPlanRequest;
use App\Http\Requests\UpdateSubscriptionPlanRequest;
use App\Http\Resources\SubscriptionPlanResource;
use App\Models\SubscriptionPlan;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;

class SubscriptionPlanController extends Controller
{
    //
    public function index(Request $request) : JsonResponse
    {
        $subscriptions = SubscriptionPlan::all();

        return $this->success(
            message:"list subscriptions",
            data: SubscriptionPlanResource::collection($subscriptions),
            code:HttpStatusCode::SUCCESSFUL->value
        );
    }

    public function store(CreateSubscriptionPlanRequest $request) : JsonResponse
    {

        $subscription = SubscriptionPlan::create(array_merge(
            Arr::except($request->validated(),'limited_to'),['limited_to' => json_encode($request->validated()['limited_to'])]));

        return $this->success(
            message:"subscription created",
            data: new SubscriptionPlanResource($subscription),
            code:HttpStatusCode::SUCCESSFUL->value
        );
    }

    public function show(SubscriptionPlan $subscriptionPlan) : JsonResponse
    {
        return $this->success(
            message:"subscription retrieved",
            data: new SubscriptionPlanResource($subscriptionPlan),
            code:HttpStatusCode::SUCCESSFUL->value
        );
    }

    public function update(UpdateSubscriptionPlanRequest $request,SubscriptionPlan $subscriptionPlan) : JsonResponse
    {
        $subscriptionPlan->update(array_merge(
            Arr::except($request->validated(),'limited_to'),['limited_to' => json_encode($request->validated()['limited_to'])]));

        return $this->success(
            message:"subscription updated",
            data: new SubscriptionPlanResource($subscriptionPlan),
            code:HttpStatusCode::SUCCESSFUL->value
        );
    }

    public function destroy(SubscriptionPlan $subscriptionPlan) : JsonResponse
    {
        $subscriptionPlan->delete();
        return $this->success(
            message:"subscription deleted",
            data: new SubscriptionPlanResource($subscriptionPlan),
            code:HttpStatusCode::SUCCESSFUL->value
        );
    }

    public function getPremiumPlan() : JsonResponse
    {
        $subscriptionPlan = SubscriptionPlan::where('type', 'paid')->first();

        $durationsArray = [1, 3, 6, 12];
        $durations = [];

        foreach ($durationsArray as $duration) {
            $durations[] =  [
                'title' => "{$duration} month" . ($duration > 1 ? 's' : ''),
                'amount' => number_format($subscriptionPlan->applyDiscount($duration) / 100),
                'discount' => $subscriptionPlan->discount,
                'monthly_price' => number_format($subscriptionPlan->monthly_price)
            ];
        }

        return $this->success(
            message:"subscription retrieved",
            data: [
                'subscription' => new SubscriptionPlanResource($subscriptionPlan),
                'durations' => $durations
            ],
            code:HttpStatusCode::SUCCESSFUL->value
        );
    }
}
