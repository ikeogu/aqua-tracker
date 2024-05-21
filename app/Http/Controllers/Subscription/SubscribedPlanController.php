<?php

namespace App\Http\Controllers\Subscription;

use App\Enums\HttpStatusCode;
use App\Http\Clients\PaystackClient;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\SubscribedPlanResource;
use App\Models\PaymentInfo;
use App\Models\SubscribedPlan;
use App\Models\SubscriptionPlan;
use App\Notifications\PaymentInfoNotification;
use App\Services\PaymentService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Validator;
use phpDocumentor\Reflection\PseudoTypes\False_;
use Ramsey\Uuid\Builder\FallbackBuilder;
use Spatie\QueryBuilder\QueryBuilder;

use function App\Helpers\currentPlan;

class SubscribedPlanController extends Controller
{
    //

    public function __construct(
        public PaymentService $paymentService,
        public PaystackClient $paystackClient
    )
    {

    }

    public function index(Request $request) : JsonResponse
    {
        $subscribedPlans = QueryBuilder::for(SubscribedPlan::class)
        ->allowedFilters(['type','duration'])
        ->latest()
        ->paginate($request->per_page ?: 10);

        return $this->success(
            message:"Subscribed plans",
            data: SubscribedPlanResource::collection($subscribedPlans)->response()->getData(true),
            code: HttpStatusCode::SUCCESSFUL->value
        );
    }

    public function upgradePlan(Request $request) : JsonResponse
    {
        $user = auth()->user();

        $request->validate([
            'no_of_months' => ['required', 'in:1,3,6,12']
        ]);

    /*     if(SubscribedPlan::where('tenant_id', $user->tenant->id)->where('type','!=', 'free')->where('status', 'active')->exists()){
            return $this->error("You have a subscription still running");
        } */

        //SubscribedPlan::where('tenant_id', $user->tenant->id)->where('status', 'active')->first()->update(['status', 'expired']);

        $paystackData =  $this->paymentService->upgradeUserPlan($request,$user);

        return $this->success(
            message:"Payment in progress",
            data:[
                'paystack_data' => $paystackData
            ],
            code: HttpStatusCode::SUCCESSFUL->value
        );

    }


    public function verifyPayment(Request $request) : JsonResponse
    {
        $request->validate(
            ['reference' => ['required', 'string', 'exists:subscribed_plans,reference']]
        );

        $response = $this->paystackClient->verifyTransaction($request->reference);

        if(array_key_exists('status', $response) && $response['status'] === false){
            return $this->error(
                'Payment was not completed'
            );
        }

        $payment = SubscribedPlan::where('reference', $response->reference)->first();

        PaymentInfo::create([
            'tenant_id' => $payment->tenant_id,
            'authorization' => json_encode($response->authorization),
            'auto_renewal' => false
        ]);

       $payment->update([
            'status' =>'active',
            'payment_method' => $response->channel,

        ]);

        $payment->tenant->user->notify(new PaymentInfoNotification($payment));

        return $this->success(
            message:"Payment successful",
            data:null,
            code: HttpStatusCode::SUCCESSFUL->value
        );


    }


    public function billingRecords(Request $request) : JsonResponse
    {
        $user = auth()->user();

        $tenant = $user->tenant;

        $subscribedPlans = $tenant->subscribedPlans()->latest()->paginate($request->per_page ?: 20);

        $paymentInfo = PaymentInfo::where('tenant_id', $tenant->id)->latest()->first();
        $authorization  = json_decode($paymentInfo?->authorization);
        $data = [
            'current_plan' => [
                'title' => currentPlan()->subscriptionPlan->title ?? null,
                'type' => currentPlan()->type ?? null,
                'amount' => currentPlan()->amount ?? null,
            ],
            'payment_info' => [
                'id' => $paymentInfo?->id,
                'auto_renewal' => $paymentInfo?->auto_renewal,
                "bin" => $authorization->bin ?? '',
                "last4" => $authorization->last4 ?? '',
                "exp_month" =>  $authorization->exp_month ?? '',
                "exp_year" => $authorization->exp_year ??  '',
                "channel" => $authorization->channel ?? '',
                "card_type" => $authorization->card_type ?? '',
                "bank" => $authorization->bank ?? '',
                "brand" => $authorization->brand ?? '',
            ],
            'transaction_history' => SubscribedPlanResource::collection($subscribedPlans)->response()->getData(true)
        ];

        return $this->success(
            message:"Transaction history retrieved",
            data:$data,
            code: HttpStatusCode::SUCCESSFUL->value
        );

     }

     public function activateAutoRenewal(Request $request) : JsonResponse
     {
        $request->validate([
            'payment_info_id' => ['required', 'exists:payment_infos,id'],
            'activate_autorenew' => ['required', 'boolean']
        ]);
        $paymentInfo = PaymentInfo::find($request->payment_info_id);
        $paymentInfo->update(['auto_renewal' => $request->activate_autorenew]);

        return $this->success(
            message:"Success",
            code: HttpStatusCode::SUCCESSFUL->value
        );
     }
}
