<?php

namespace App\Traits;

use App\Exceptions\InvalidOrExpiredOtp;
use App\Enums\Otp;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

trait HasOtp
{
    public function generateOtpFor(Otp $for): void
    {
        $this->otp()->updateOrCreate([
            'user_id' => $this->id,
            'for' => $for,
        ], [
            'otp' => rand(1000, 9999),
            'expires_at' => now()->addMinutes(6),
        ]);
    }

    public function verifyOtpFor(Otp $for, int $otp): bool
    {

        $userOtp = $this->getOtpFor($for);

        if ($userOtp && $userOtp->otp == $otp) {
            if ($userOtp->expires_at > now()) {
                $userOtp->delete();
            }
            return true;
        } else {
            throw new InvalidOrExpiredOtp('Invalid OTP provided', 422);
        }
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
