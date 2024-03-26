<?php

namespace App\Enums;

use App\Traits\EnumValues;

enum Otp: string
{
    use EnumValues;

    case EMAIL_VERIFICATION = 'email_verification';
    case TWO_FACTOR_AUTHENTICATION = 'two_factor_authentication';
}
