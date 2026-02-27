# Production Security Checklist

## 1. Environment
- Set `APP_ENV=production`.
- Set `APP_DEBUG=false`.
- Set `APP_URL=https://your-domain.tld`.
- Set `SECURITY_FORCE_HTTPS=true`.
- Set `SESSION_SECURE_COOKIE=true`.
- Set `SECURITY_2FA_ENABLED=true` for admin-heavy environments.
- Set `RBAC_BOOTSTRAP_EMAIL` and `RBAC_BOOTSTRAP_PASSWORD` before first seed.

## 2. Build / Cache / Optimize
- Run: `php artisan migrate --force`.
- Run: `php artisan db:seed --class=RolesAndPermissionsSeeder --force`.
- Run: `php artisan config:cache`.
- Run: `php artisan route:cache`.
- Run: `php artisan event:cache`.
- Run: `composer install --no-dev --optimize-autoloader`.

## 3. Queue and Heavy Jobs
- Use queue workers for PDF/report heavy workloads:
  - `php artisan queue:work --queue=default --tries=3 --timeout=120`.
- Use a process manager (Supervisor/systemd) for worker restart and health.

## 4. Storage and File Security
- Keep uploads in private storage (`storage/app/private`).
- Use signed URLs for all sensitive downloads.
- Do not expose raw storage paths in templates.

## 5. RBAC
- Seed and verify roles:
  - `SuperAdmin`, `Admin`, `DVCommittee`, `Evaluator`, `Auditor`.
- Audit that every admin route has `permission:*` middleware.

## 6. Logging and Monitoring
- Keep log channel stack on `daily`.
- Forward logs to central monitoring (ELK/CloudWatch/Datadog).
- Review `failed_login_attempts` and `audit_logs` regularly.

## 7. Optional API Hardening (Sanctum)
Install and configure Sanctum for admin APIs:

```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\\Sanctum\\SanctumServiceProvider"
php artisan migrate --force
```

Example protected API route:

```php
Route::middleware(['auth:sanctum', 'abilities:view_reports', 'throttle:30,1'])
    ->get('/admin/reports/summary', [ReportApiController::class, 'summary']);
```
