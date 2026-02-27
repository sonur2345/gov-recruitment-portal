<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Services\AuditService;
use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function __construct(
        private readonly AuditService $auditService
    ) {
    }

    public function index(Request $request, ReportService $reportService)
    {
        $filters = $this->validatedFilters($request, $reportService);
        $dashboardData = $reportService->getDashboardData($filters);

        $posts = Post::query()->orderBy('id')->get(['id']);
        foreach (['name', 'title', 'post_name', 'code'] as $column) {
            if (\Illuminate\Support\Facades\Schema::hasColumn('posts', $column)) {
                $posts = Post::query()->orderBy('id')->get(array_unique(['id', $column]));
                break;
            }
        }

        return view('admin.reports.dashboard', array_merge($dashboardData, [
            'filters' => $filters,
            'categories' => $reportService->allowedCategories(),
            'posts' => $posts,
        ]));
    }

    public function exportCsv(Request $request, ReportService $reportService): StreamedResponse
    {
        $filters = $this->validatedFilters($request, $reportService);
        $rows = $reportService->getExportRows($filters);

        $this->auditService->log(
            action: 'report_export_csv',
            modelType: 'report',
            modelId: 0,
            newData: ['filters' => $filters, 'rows' => $rows->count()],
            request: $request,
            userId: $request->user()->id
        );

        $filename = 'reports-' . now()->format('Ymd-His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        return response()->stream(function () use ($rows): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Application No',
                'Candidate Name',
                'Email',
                'Post',
                'Category',
                'Status',
                'Education %',
                'Experience Marks',
                'Skill Marks',
                'Total',
                'Rank',
                'Applied At',
            ]);

            foreach ($rows as $row) {
                fputcsv($handle, [
                    $row['application_no'],
                    $row['candidate_name'],
                    $row['email'],
                    $row['post'],
                    $row['category'],
                    $row['status'],
                    $row['education_percentage'],
                    $row['experience_marks'],
                    $row['skill_marks'],
                    $row['total_marks'],
                    $row['rank'],
                    $row['created_at'],
                ]);
            }
            fclose($handle);
        }, 200, $headers);
    }

    public function exportExcel(Request $request, ReportService $reportService): StreamedResponse
    {
        $filters = $this->validatedFilters($request, $reportService);
        $rows = $reportService->getExportRows($filters);

        $this->auditService->log(
            action: 'report_export_excel',
            modelType: 'report',
            modelId: 0,
            newData: ['filters' => $filters, 'rows' => $rows->count()],
            request: $request,
            userId: $request->user()->id
        );

        $filename = 'reports-' . now()->format('Ymd-His') . '.xls';
        $headers = [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        return response()->stream(function () use ($rows): void {
            echo "<table border='1'>";
            echo '<tr>';
            foreach ([
                'Application No',
                'Candidate Name',
                'Email',
                'Post',
                'Category',
                'Status',
                'Education %',
                'Experience Marks',
                'Skill Marks',
                'Total',
                'Rank',
                'Applied At',
            ] as $head) {
                echo '<th>' . e($head) . '</th>';
            }
            echo '</tr>';

            foreach ($rows as $row) {
                echo '<tr>';
                foreach ($row as $cell) {
                    echo '<td>' . e((string) $cell) . '</td>';
                }
                echo '</tr>';
            }
            echo '</table>';
        }, 200, $headers);
    }

    public function exportPdf(Request $request, ReportService $reportService)
    {
        $filters = $this->validatedFilters($request, $reportService);
        $dashboardData = $reportService->getDashboardData($filters);
        $rows = $reportService->getExportRows($filters);

        $this->auditService->log(
            action: 'report_export_pdf',
            modelType: 'report',
            modelId: 0,
            newData: ['filters' => $filters, 'rows' => $rows->count()],
            request: $request,
            userId: $request->user()->id
        );

        $pdf = Pdf::loadView('admin.reports.export-pdf', [
            'summary' => $dashboardData['summary'],
            'postWiseStats' => $dashboardData['postWiseStats'],
            'rows' => $rows,
            'filters' => $filters,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('reports-' . now()->format('Ymd-His') . '.pdf');
    }

    private function validatedFilters(Request $request, ReportService $reportService): array
    {
        if ($request->filled('category')) {
            $request->merge([
                'category' => strtoupper((string) $request->input('category')),
            ]);
        }

        return $request->validate([
            'post_id' => ['nullable', 'integer', 'exists:posts,id'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'category' => ['nullable', Rule::in($reportService->allowedCategories())],
        ]);
    }
}
