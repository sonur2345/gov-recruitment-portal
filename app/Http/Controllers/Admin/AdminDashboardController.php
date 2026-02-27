<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\DemandDraft;
use App\Models\Grievance;
use App\Models\Notification;
use App\Services\ReportService;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(ReportService $reportService): View
    {
        $dashboardData = $reportService->getDashboardData([]);

        $adminStats = [
            'notifications' => Schema::hasTable('notifications') ? Notification::query()->count() : 0,
            'posts' => (int) ($dashboardData['summary']['total_posts'] ?? 0),
            'applications' => (int) ($dashboardData['summary']['total_applications'] ?? 0),
            'under_scrutiny' => (int) ($dashboardData['summary']['under_scrutiny'] ?? 0),
            'eligible' => (int) ($dashboardData['summary']['eligible_candidates'] ?? 0),
            'shortlisted' => (int) ($dashboardData['summary']['shortlisted'] ?? 0),
            'qualified' => (int) ($dashboardData['summary']['qualified'] ?? 0),
            'selected' => (int) ($dashboardData['summary']['selected'] ?? 0),
            'final_selected' => (int) ($dashboardData['summary']['final_selected'] ?? 0),
            'rejected' => (int) ($dashboardData['summary']['rejected'] ?? 0),
            'dd_pending' => Schema::hasTable('demand_drafts')
                ? (int) DemandDraft::query()->where('status', 'pending')->count()
                : 0,
            'pending_grievances' => Schema::hasTable('grievances')
                ? (int) Grievance::query()->whereIn('status', ['open', 'in_progress'])->count()
                : 0,
        ];

        $postColumns = ['id'];
        foreach (['name', 'title', 'post_name'] as $column) {
            if (Schema::hasColumn('posts', $column)) {
                $postColumns[] = $column;
            }
        }

        $recentApplications = Application::query()
            ->with([
                'user:id,name,email',
                'post' => fn ($q) => $q->select(array_unique($postColumns)),
            ])
            ->latest('id')
            ->take(10)
            ->get();

        return view('admin.dashboard', array_merge($dashboardData, [
            'adminStats' => $adminStats,
            'recentApplications' => $recentApplications,
        ]));
    }
}
