<?php

namespace App\Services\Webhook\Handler;

use App\Services\PaymentService;
use Illuminate\Support\Facades\Log;
use Spatie\WebhookClient\Models\WebhookCall;
use Spatie\WebhookClient\Jobs\ProcessWebhookJob;

class PaystackHandler extends ProcessWebhookJob
{
    public function handle(WebhookCall $webhookCall, PaymentService $paymentService)
    {
        $payload = $webhookCall->payload;

        Log::info('Webhook call with id `{$webhookCall->id}` has been received');
        Log::info('Payload: {$payload}');

        // perform the work here

        match($payload['event']) {
            'charge.success' => $paymentService->verifyPayment($payload['data']),
            'paymentrequest.success' => $paymentService->verifyPayment($payload['data']),
            'transfer.success' => $paymentService->verifyPayment($payload['data']),
            default => 'Unknown event'
        };

        Log::info('Webhook call with id `{$webhookCall->id}` has been processed');
    }
}