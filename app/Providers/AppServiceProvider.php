<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Password::defaults(function () {
            $rule = Password::min((int) config('security.password_min_length', 12))
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols();

            if ((bool) config('security.password_check_uncompromised', false)) {
                $rule = $rule->uncompromised();
            }

            return $rule;
        });

        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute((int) config('security.login_max_attempts', 5))
                ->by((string) $request->string('email')->lower() . '|' . $request->ip());
        });

        RateLimiter::for('otp', function (Request $request) {
            return Limit::perMinute((int) config('security.two_factor_rate_limit', 5))
                ->by((string) ($request->user()?->id ?? 'guest') . '|' . $request->ip());
        });

        if (config('security.force_https') && app()->isProduction()) {
            URL::forceScheme('https');
        }
    }
}
