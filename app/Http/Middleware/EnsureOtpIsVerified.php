<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOtpIsVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!config('security.two_factor_enabled', false) || !$request->user()) {
            return $next($request);
        }

        if ($request->session()->get('two_factor_passed') === true) {
            return $next($request);
        }

        if ($request->routeIs([
            'logout',
            'verification.notice',
            'verification.verify',
            'verification.send',
            'two-factor.challenge',
            'two-factor.verify',
            'two-factor.resend',
        ])) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Two-factor verification required.',
            ], 423);
        }

        return redirect()
            ->route('two-factor.challenge')
            ->with('status', 'Please complete OTP verification to continue.');
    }
}
