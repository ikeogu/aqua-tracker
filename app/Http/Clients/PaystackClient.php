<?php

namespace App\Http\Clients;

use App\DataTransferObjects\VerifyPaymentDto;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Log;

class PaystackClient extends PendingRequest
{

    public function initiateTransaction( array $data) : mixed
    {
        $response = $this->post("/transaction/initialize", $data);

        return $response->json();
        
    }

    public function verifyTransaction(string $reference): mixed

    {

        $response = $this->get("/transaction/verify/$reference");

        try {

            $response = $this->get("/transaction/verify/$reference");

            return  VerifyPaymentDto::create($response->json());

        } catch (GuzzleException $e) {
            //throw $th;
            Log::debug($e->getMessage());
            return [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }

    }

   public function chargeCard(string $authorization_code, mixed $amount, string $email) : mixed
    {

            $data = [
                'authorization_code' => $authorization_code,
                'email' => $email,
                'amount' => $amount * 100,
            ];

        try {

            $response = $this->post("transaction/charge_authorization", $data);
            return VerifyPaymentDto::create($response->json());

        } catch (GuzzleException $e) {
            //throw $th;
            Log::debug($e->getMessage());
            return [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
