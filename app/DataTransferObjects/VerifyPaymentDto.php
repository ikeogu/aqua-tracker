<?php

namespace App\DataTransferObjects;

use Illuminate\Support\Facades\Log;
use Spatie\LaravelData\Data;

class VerifyPaymentDto extends Data
{
    public function __construct(
        public bool $status,
        public string $reference,
        public string $channel,
        public mixed $authorization,

    ){}


    public static function create(array $data) : self {
        Log::debug($data);
        return VerifyPaymentDto::from([
            'status' => $data['status'] ,
            'reference' => $data['data']['reference'],
            'channel' => $data['data']['channel'],
            'authorization' => $data['data']['authorization'],
        ]);


    }
}

