<?php

namespace App\Traits;

use App\Exceptions\InvalidOrExpiredOtp;
use App\Enums\Otp;
use Carbon\Carbon;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

trait HasOtp
{
    public function generateOtpFor(Otp $for): void
    {
        $otp = random_int(1000, 9999); // safer than rand()

        $this->otp()->updateOrCreate(
            [
                'user_id' => $this->id,
                'for'     => $for->value, // store enum value, not object
            ],
            [
                'otp'        => $otp,
                'expires_at' => now()->addMinutes(6),
            ]
        );
    }

    public function verifyOtpFor(Otp $for, int $otp): bool
    {
        $userOtp = $this->getOtpFor($for);

        if (! $userOtp) {
            throw new InvalidOrExpiredOtp('OTP not found', 404);
        }

        if ($userOtp->otp !== $otp) {
            throw new InvalidOrExpiredOtp('Invalid OTP provided', 422);
        }

        if (Carbon::parse($userOtp->expires_at)->isPast()) {
            $userOtp->delete();
            throw new InvalidOrExpiredOtp('OTP has expired', 422);
        }
        // OTP is valid → delete after successful verification
        $userOtp->delete();

        return true;
    }


    public function hasVerifiedOtpFor(Otp $for): bool
    {
        return is_null($this->getOtpFor($for)) ? true : false;
    }

    public function canRequestNewOtpFor(Otp $for): bool
    {

        $userOtp = $this->getOtpFor($for);

        if (!$userOtp && !$this->hasVerifiedEmail()) {
            return true;
        }

        if (now() < $userOtp?->updated_at?->addMinutes(1)) {
            throw new TooManyRequestsHttpException(5, 'Please wait before requesting new code', null, 429);
        }

        return true;
    }

    public function getOtpFor(Otp $for): mixed
    {

        return $this->otp()->where('for', $for)->first();
    }
}
