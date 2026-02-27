# Government Recruitment Portal

Laravel 12 based recruitment portal built for government workflows.

## Highlights
- Notifications and post configuration with PDFs and versions.
- Postal intake with Demand Draft verification.
- Scrutiny, shortlisting, skill tests, and merit generation.
- Document verification and appointment orders.
- Grievances, audit logs, and reports.
- RBAC with OTP verification.

## Setup (Local)
1. Copy `.env.example` to `.env` and set DB credentials.
2. Install dependencies: `composer install` and `npm install`.
3. Generate key: `php artisan key:generate`.
4. Run migrations and seeds: `php artisan migrate --seed`.
5. Build assets: `npm run build` or `npm run dev`.
