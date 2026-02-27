<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureSessionIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) {
            return $next($request);
        }

        $timeoutMinutes = max((int) config('security.session_timeout_minutes', 30), 1);
        $now = now()->getTimestamp();
        $lastActivity = (int) $request->session()->get('last_activity_at', $now);

        if (($now - $lastActivity) > ($timeoutMinutes * 60)) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Session expired due to inactivity. Please login again.']);
        }

        $request->session()->put('last_activity_at', $now);

        return $next($request);
    }
}
