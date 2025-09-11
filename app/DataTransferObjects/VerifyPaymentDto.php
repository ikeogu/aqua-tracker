<?php

namespace App\DataTransferObjects;

use Illuminate\Support\Facades\Log;
use Spatie\LaravelData\Data;

class VerifyPaymentDto extends Data
{
    public function __construct(
        public bool $status,
        public string $channel,
        public mixed $authorization,
        public ?string $reference = null,
        public ?string $message = null

    ){}


    public static function create(array $data) : self {

        return VerifyPaymentDto::from([
            'status' => $data['status'] ,
            'channel' => $data['data']['channel'],
            'authorization' => $data['data']['authorization'],
            'reference' => $data['data']['reference'] ?? null,
            'message' => $data['message'] ?? ''
        ]);


    }
}

