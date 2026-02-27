<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Candidate\ApplicationController;
use App\Http\Controllers\Candidate\AdmitCardController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\ScrutinyController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\DemandDraftController;
use App\Http\Controllers\Admin\PostalIntakeController;
use App\Http\Controllers\Admin\GrievanceController as AdminGrievanceController;
use App\Http\Controllers\Admin\SkillTestController;
use App\Http\Controllers\Admin\DocumentVerificationController;
use App\Http\Controllers\Admin\AppointmentOrderController;
use App\Http\Controllers\Admin\ShortlistController;
use App\Http\Controllers\Admin\MeritController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\FileDownloadController;
use App\Http\Controllers\Candidate\GrievanceController as CandidateGrievanceController;
use App\Models\Application;
use App\Models\Post;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;

Route::get('/', function () {
    $activePosts = collect();
    $importantNotice = 'Candidates are advised to complete applications before the closing date. Incomplete submissions will not be considered.';

    if (Schema::hasTable('posts') && Schema::hasTable('notifications')) {
        $hasStatusColumn = Schema::hasColumn('notifications', 'status');
        $hasStartDateColumn = Schema::hasColumn('notifications', 'start_date');
        $hasEndDateColumn = Schema::hasColumn('notifications', 'end_date');

        $activePostsQuery = Post::query()
            ->with('notification')
            ->latest('id');

        if ($hasStatusColumn || $hasStartDateColumn || $hasEndDateColumn) {
            $activePostsQuery->whereHas('notification', function ($query) use (
                $hasStatusColumn,
                $hasStartDateColumn,
                $hasEndDateColumn
            ): void {
                if ($hasStatusColumn) {
                    $query->where('status', 'published');
                }

                if ($hasStartDateColumn) {
                    $query->whereDate('start_date', '<=', now()->toDateString());
                }

                if ($hasEndDateColumn) {
                    $query->whereDate('end_date', '>=', now()->toDateString());
                }
            });
        }

        $activePosts = $activePostsQuery->take(12)->get();
    }

    return view('home', compact('activePosts', 'importantNotice'));
})->name('home');

Route::get('/dashboard', function () {
    $applications = new LengthAwarePaginator(
        items: [],
        total: 0,
        perPage: 10,
        currentPage: 1,
        options: ['path' => request()->url()]
    );

    $summary = [
        'total' => 0,
        'under_scrutiny' => 0,
        'shortlisted' => 0,
        'selected' => 0,
    ];

    if (Schema::hasTable('applications')) {
        $baseQuery = Application::query()
            ->where('user_id', auth()->id());

        $applications = (clone $baseQuery)
            ->with(['post.notification', 'appointmentOrder'])
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        $summary = [
            'total' => (clone $baseQuery)->count(),
            'under_scrutiny' => (clone $baseQuery)->where('status', 'under_scrutiny')->count(),
            'shortlisted' => (clone $baseQuery)->where('status', 'shortlisted')->count(),
            'selected' => (clone $baseQuery)->whereIn('status', ['selected', 'final_selected'])->count(),
        ];
    }

    return view('dashboard', compact('applications', 'summary'));
})->middleware(['auth', 'verified', 'session.timeout', 'otp.verified'])->name('dashboard');

Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'session.timeout', 'otp.verified', 'role_or_permission:SuperAdmin|Admin'])
    ->name('admin.dashboard');

Route::middleware(['auth', 'verified', 'session.timeout', 'otp.verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/notifications/{notification}/pdf', [FileDownloadController::class, 'notificationPdf'])
    ->middleware(['signed', 'throttle:60,1'])
    ->name('notifications.pdf.download');

Route::middleware(['auth', 'verified', 'session.timeout', 'otp.verified'])->group(function () {

    Route::get('/apply', [ApplicationController::class, 'create'])->name('application.create');
    Route::get('/apply/preview', [ApplicationController::class, 'preview'])->name('application.preview');
    Route::get('/apply/{post}', [ApplicationController::class, 'create'])->name('application.create.post');

    Route::post('/apply', [ApplicationController::class, 'store'])->name('application.store');
    Route::get('/applications/{application}/admit-card', [AdmitCardController::class, 'download'])
        ->name('applications.admit-card.download');
    Route::get('/documents/{document}/download', [FileDownloadController::class, 'applicationDocument'])
        ->middleware(['signed', 'throttle:30,1'])
        ->name('files.documents.download');
    Route::get('/grievance-documents/{document}/download', [FileDownloadController::class, 'grievanceDocument'])
        ->middleware(['signed', 'throttle:30,1'])
        ->name('files.grievances.download');

    Route::get('/grievances', [CandidateGrievanceController::class, 'index'])->name('grievances.index');
    Route::get('/grievances/create', [CandidateGrievanceController::class, 'create'])->name('grievances.create');
    Route::post('/grievances', [CandidateGrievanceController::class, 'store'])->name('grievances.store');
    Route::get('/grievances/{grievance}', [CandidateGrievanceController::class, 'show'])->name('grievances.show');

    Route::prefix('admin')->name('admin.')->middleware('role_or_permission:SuperAdmin|Admin|DEO|ScrutinyOfficer|MeritAdmin|DVCommittee|Evaluator|Auditor')->group(function () {
        Route::middleware('permission:log_postal_intake')->group(function () {
            Route::get('/postal-intake', [PostalIntakeController::class, 'index'])->name('postal-intake.index');
            Route::get('/postal-intake/create', [PostalIntakeController::class, 'create'])->name('postal-intake.create');
            Route::post('/postal-intake', [PostalIntakeController::class, 'store'])->name('postal-intake.store');
        });

        Route::middleware('permission:scrutinize_applications')->group(function () {
            Route::get('/scrutiny', [ScrutinyController::class, 'index'])->name('scrutiny.index');
            Route::get('/scrutiny/{application}', [ScrutinyController::class, 'show'])->name('scrutiny.show');
            Route::patch('/scrutiny/{application}', [ScrutinyController::class, 'update'])->name('scrutiny.update');
        });

        Route::middleware('permission:verify_dd')->group(function () {
            Route::get('/demand-drafts', [DemandDraftController::class, 'index'])->name('demand-drafts.index');
            Route::patch('/demand-drafts/{demandDraft}/valid', [DemandDraftController::class, 'markValid'])->name('demand-drafts.valid');
            Route::patch('/demand-drafts/{demandDraft}/invalid', [DemandDraftController::class, 'markInvalid'])->name('demand-drafts.invalid');
        });

        Route::middleware('permission:evaluate_skill_test')->group(function () {
            Route::get('/skill-tests', [SkillTestController::class, 'index'])->name('skill-tests.index');
            Route::get('/skill-tests/{application}', [SkillTestController::class, 'show'])->name('skill-tests.show');
            Route::post('/skill-tests/{application}', [SkillTestController::class, 'store'])->name('skill-tests.store');
        });

        Route::middleware('permission:generate_merit')->group(function () {
            Route::get('/shortlists', [ShortlistController::class, 'index'])->name('shortlists.index');
            Route::post('/shortlists/generate', [ShortlistController::class, 'generate'])->name('shortlists.generate');

            Route::get('/merit', [MeritController::class, 'index'])->name('merit.index');
            Route::post('/merit/generate', [MeritController::class, 'generate'])->name('merit.generate');
        });

        Route::middleware('permission:verify_documents')->group(function () {
            Route::get('/document-verifications', [DocumentVerificationController::class, 'index'])->name('document-verifications.index');
            Route::get('/document-verifications/{application}', [DocumentVerificationController::class, 'show'])->name('document-verifications.show');
            Route::post('/document-verifications/{application}', [DocumentVerificationController::class, 'store'])->name('document-verifications.store');
        });

        Route::middleware('permission:generate_appointment')->group(function () {
            Route::get('/appointment-orders', [AppointmentOrderController::class, 'index'])->name('appointment-orders.index');
            Route::post('/appointment-orders/{application}/generate', [AppointmentOrderController::class, 'generate'])->name('appointment-orders.generate');
            Route::get('/appointment-orders/{appointmentOrder}/download', [AppointmentOrderController::class, 'download'])
                ->middleware(['signed', 'throttle:30,1'])
                ->name('appointment-orders.download');
        });

        Route::middleware('permission:view_reports')->group(function () {
            Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
            Route::get('/reports/export/csv', [ReportController::class, 'exportCsv'])->name('reports.export.csv');
            Route::get('/reports/export/excel', [ReportController::class, 'exportExcel'])->name('reports.export.excel');
            Route::get('/reports/export/pdf', [ReportController::class, 'exportPdf'])->name('reports.export.pdf');
        });

        Route::middleware('permission:manage_grievances')->group(function () {
            Route::get('/grievances', [AdminGrievanceController::class, 'index'])->name('grievances.index');
            Route::get('/grievances/{grievance}', [AdminGrievanceController::class, 'show'])->name('grievances.show');
            Route::patch('/grievances/{grievance}', [AdminGrievanceController::class, 'update'])->name('grievances.update');
        });

        Route::middleware('permission:view_audit_logs')->group(function () {
            Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
        });

        Route::middleware('permission:manage_posts')->group(function () {
            Route::resource('notifications', NotificationController::class);
            Route::resource('posts', PostController::class);
        });
    });

});

require __DIR__.'/auth.php';
