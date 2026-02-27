<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\TwoFactorOtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request, TwoFactorOtpService $twoFactorOtpService): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();
        $request->session()->put('last_activity_at', now()->getTimestamp());

        $user = $request->user();

        if (!$user->hasVerifiedEmail()) {
            return redirect()
                ->route('verification.notice')
                ->with('error', 'Please verify your email address before continuing.');
        }

        if (config('security.two_factor_enabled', false)) {
            $request->session()->put('two_factor_passed', false);
            $twoFactorOtpService->issueForUser($user);

            return redirect()->route('two-factor.challenge');
        }

        $request->session()->put('two_factor_passed', true);

        return redirect()
            ->to(route($user->dashboardRouteName(), absolute: false))
            ->with('success', 'Login successful.');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'You have been logged out successfully.');
    }
}
