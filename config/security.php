<?php

return [
    'force_https' => env('SECURITY_FORCE_HTTPS', true),

    'session_timeout_minutes' => (int) env('SESSION_TIMEOUT_MINUTES', 30),

    'password_min_length' => (int) env('SECURITY_PASSWORD_MIN_LENGTH', 12),
    'password_check_uncompromised' => (bool) env('SECURITY_PASSWORD_UNCOMPROMISED', false),
    'login_max_attempts' => (int) env('SECURITY_LOGIN_MAX_ATTEMPTS', 5),

    'two_factor_enabled' => (bool) env('SECURITY_2FA_ENABLED', false),
    'two_factor_expiry_minutes' => (int) env('SECURITY_2FA_EXPIRY_MINUTES', 10),
    'two_factor_max_attempts' => (int) env('SECURITY_2FA_MAX_ATTEMPTS', 5),
    'two_factor_rate_limit' => (int) env('SECURITY_2FA_RATE_LIMIT', 5),
];
