<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\VerifyTwoFactorOtpRequest;
use App\Services\TwoFactorOtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TwoFactorChallengeController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        if (!config('security.two_factor_enabled', false)) {
            return redirect()->to(route($request->user()->dashboardRouteName(), absolute: false));
        }

        if ($request->session()->get('two_factor_passed') === true) {
            return redirect()->to(route($request->user()->dashboardRouteName(), absolute: false));
        }

        return view('auth.two-factor-challenge');
    }

    public function store(VerifyTwoFactorOtpRequest $request, TwoFactorOtpService $twoFactorOtpService): RedirectResponse
    {
        abort_unless(config('security.two_factor_enabled', false), 404);

        $validated = $request->validated();

        if (!$twoFactorOtpService->verifyForUser($request->user(), (string) $validated['otp'])) {
            return back()->withErrors([
                'otp' => 'Invalid or expired OTP.',
            ]);
        }

        $request->session()->put('two_factor_passed', true);

        return redirect()
            ->to(route($request->user()->dashboardRouteName(), absolute: false))
            ->with('success', 'Login successful.');
    }

    public function resend(Request $request, TwoFactorOtpService $twoFactorOtpService): RedirectResponse
    {
        abort_unless(config('security.two_factor_enabled', false), 404);

        $twoFactorOtpService->issueForUser($request->user());

        return back()->with('status', 'A new OTP has been sent to your email.');
    }
}
