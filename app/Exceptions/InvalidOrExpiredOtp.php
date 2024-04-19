<?php

namespace App\Exceptions;

use App\Traits\RespondsWithHttpStatus;
use Exception;
use Illuminate\Http\Request;

class InvalidOrExpiredOtp extends Exception
{

    use RespondsWithHttpStatus;

    public function render(Request $request): mixed
    {



        return $this->error(
            message: $this->message,
            error: [
                'code' => $this->message,
            ],
            code: $this->code
        );

    }
}
