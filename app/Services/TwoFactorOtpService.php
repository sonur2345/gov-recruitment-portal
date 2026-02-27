<?php

namespace App\Services;

use App\Models\TwoFactorOtp;
use App\Models\User;
use App\Notifications\TwoFactorOtpNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TwoFactorOtpService
{
    public function issueForUser(User $user): void
    {
        $expiryMinutes = (int) config('security.two_factor_expiry_minutes', 10);
        $otp = (string) random_int(100000, 999999);

        TwoFactorOtp::query()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'otp_hash' => Hash::make($otp),
                'expires_at' => now()->addMinutes($expiryMinutes),
                'verified_at' => null,
                'attempt_count' => 0,
            ]
        );

        $user->notify(new TwoFactorOtpNotification($otp, $expiryMinutes));
    }

    public function verifyForUser(User $user, string $otp): bool
    {
        return DB::transaction(function () use ($user, $otp): bool {
            $record = TwoFactorOtp::query()
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->first();

            if (!$record) {
                return false;
            }

            if ($record->expires_at->isPast()) {
                return false;
            }

            if ($record->attempt_count >= (int) config('security.two_factor_max_attempts', 5)) {
                return false;
            }

            if (!Hash::check($otp, $record->otp_hash)) {
                $record->increment('attempt_count');
                return false;
            }

            $record->update([
                'verified_at' => now(),
                'attempt_count' => 0,
            ]);

            return true;
        });
    }
}
