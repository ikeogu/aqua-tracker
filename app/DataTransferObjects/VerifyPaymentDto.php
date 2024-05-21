<?php

namespace App\DataTransferObjects;

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
        return VerifyPaymentDto::from([
            'reference' => $data['status'] ,
            'reference' => $data['data']['reference'],
            'channel' => $data['data']['channel'],
            'authorization' => $data['data']['authorization'],
        ]);


    }
}

