<?php

namespace App\Services;

use App\Models\Application;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ReportService
{
    public function allowedCategories(): array
    {
        return ['GEN', 'OBC', 'SC', 'ST', 'EWS'];
    }

    public function getDashboardData(array $filters): array
    {
        $normalized = $this->normalizeFilters($filters);
        $summary = $this->getSummary($normalized);

        $postWiseStats = $this->getPostWiseStats($normalized);
        $categoryStats = $this->getCategoryStats($normalized);
        $monthlyTrend = $this->getMonthlyTrend($normalized);
        $statusDistribution = $this->getStatusDistribution($normalized);

        return [
            'summary' => $summary,
            'postWiseStats' => $postWiseStats,
            'categoryStats' => $categoryStats,
            'monthlyTrend' => $monthlyTrend,
            'statusDistribution' => $statusDistribution,
            'postSelectionChart' => [
                'labels' => $postWiseStats->pluck('post_label')->values(),
                'selected' => $postWiseStats->pluck('selected_count')->map(fn ($v) => (int) $v)->values(),
                'waiting' => $postWiseStats->pluck('waiting_count')->map(fn ($v) => (int) $v)->values(),
            ],
        ];
    }

    public function getExportRows(array $filters): Collection
    {
        $normalized = $this->normalizeFilters($filters);
        $postColumns = $this->postSelectColumns();

        $query = Application::query()
            ->with([
                'user:id,name,email',
                'post' => fn ($q) => $q->select($postColumns),
            ])
            ->orderByDesc('id');

        $this->applyApplicationFilters($query, $normalized);

        return $query->get()->map(function (Application $application): array {
            return [
                'application_no' => $application->application_no,
                'candidate_name' => $application->user?->name,
                'email' => $application->user?->email,
                'post' => $this->postLabelFromModel($application->post),
                'category' => $application->category,
                'status' => $application->status,
                'education_percentage' => $application->education_percentage,
                'experience_marks' => $application->experience_marks,
                'skill_marks' => $application->skill_marks,
                'total_marks' => $application->total_marks,
                'rank' => $application->rank,
                'created_at' => optional($application->created_at)->format('Y-m-d H:i:s'),
            ];
        });
    }

    private function getSummary(array $filters): array
    {
        $cacheKey = 'reports:summary:' . md5(json_encode($filters));

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($filters): array {
            $summaryQuery = Application::query();
            $this->applyApplicationFilters($summaryQuery, $filters);

            $summary = $summaryQuery
                ->selectRaw('COUNT(*) as total_applications')
                ->selectRaw("SUM(CASE WHEN status = 'under_scrutiny' THEN 1 ELSE 0 END) as under_scrutiny")
                ->selectRaw("SUM(CASE WHEN status = 'eligible' THEN 1 ELSE 0 END) as eligible_candidates")
                ->selectRaw("SUM(CASE WHEN status = 'shortlisted' THEN 1 ELSE 0 END) as shortlisted")
                ->selectRaw("SUM(CASE WHEN status = 'qualified' THEN 1 ELSE 0 END) as qualified")
                ->selectRaw("SUM(CASE WHEN status = 'selected' THEN 1 ELSE 0 END) as selected")
                ->selectRaw("SUM(CASE WHEN status = 'final_selected' THEN 1 ELSE 0 END) as final_selected")
                ->selectRaw("SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected")
                ->selectRaw("SUM(CASE WHEN status = 'waiting' THEN 1 ELSE 0 END) as waiting_list_count")
                ->first();

            $postCountQuery = Post::query();
            if (!empty($filters['post_id'])) {
                $postCountQuery->where('id', $filters['post_id']);
            }

            return [
                'total_posts' => (int) $postCountQuery->count(),
                'total_applications' => (int) ($summary->total_applications ?? 0),
                'under_scrutiny' => (int) ($summary->under_scrutiny ?? 0),
                'eligible_candidates' => (int) ($summary->eligible_candidates ?? 0),
                'shortlisted' => (int) ($summary->shortlisted ?? 0),
                'qualified' => (int) ($summary->qualified ?? 0),
                'selected' => (int) ($summary->selected ?? 0),
                'final_selected' => (int) ($summary->final_selected ?? 0),
                'rejected' => (int) ($summary->rejected ?? 0),
                'waiting_list_count' => (int) ($summary->waiting_list_count ?? 0),
            ];
        });
    }

    private function getPostWiseStats(array $filters): Collection
    {
        $postLabelExpr = $this->postLabelSqlExpression();

        $query = Post::query()
            ->leftJoin('applications as a', function ($join) use ($filters): void {
                $join->on('a.post_id', '=', 'posts.id');

                if (!empty($filters['category'])) {
                    $join->whereRaw('UPPER(a.category) = ?', [$filters['category']]);
                }
                if (!empty($filters['date_from'])) {
                    $join->whereDate('a.created_at', '>=', $filters['date_from']);
                }
                if (!empty($filters['date_to'])) {
                    $join->whereDate('a.created_at', '<=', $filters['date_to']);
                }
            })
            ->selectRaw('posts.id as post_id')
            ->selectRaw($postLabelExpr . ' as post_label')
            ->selectRaw('posts.total_vacancies as vacancies')
            ->selectRaw('COUNT(a.id) as applications_count')
            ->selectRaw("SUM(CASE WHEN a.status = 'selected' THEN 1 ELSE 0 END) as selected_count")
            ->selectRaw("SUM(CASE WHEN a.status = 'waiting' THEN 1 ELSE 0 END) as waiting_count")
            ->selectRaw("SUM(CASE WHEN a.status = 'rejected' THEN 1 ELSE 0 END) as rejected_count")
            ->selectRaw("SUM(CASE WHEN a.status IN ('selected','final_selected') THEN 1 ELSE 0 END) as filled_seats")
            ->groupBy('posts.id', 'posts.total_vacancies')
            ->orderBy('post_label');

        if (!empty($filters['post_id'])) {
            $query->where('posts.id', $filters['post_id']);
        }

        return $query->get();
    }

    private function getCategoryStats(array $filters): array
    {
        $query = Application::query();
        $this->applyApplicationFilters($query, $filters, skipCategoryFilter: true);

        $rows = $query
            ->selectRaw("UPPER(COALESCE(category, 'UNKNOWN')) as category")
            ->selectRaw('COUNT(*) as total_count')
            ->selectRaw("SUM(CASE WHEN status IN ('selected','final_selected') THEN 1 ELSE 0 END) as selected_count")
            ->groupBy('category')
            ->get()
            ->keyBy('category');

        $categories = $this->allowedCategories();
        $distribution = [];
        $selectedDistribution = [];
        $table = [];

        foreach ($categories as $category) {
            $total = (int) ($rows[$category]->total_count ?? 0);
            $selected = (int) ($rows[$category]->selected_count ?? 0);
            $distribution[] = $total;
            $selectedDistribution[] = $selected;
            $table[] = [
                'category' => $category,
                'total' => $total,
                'selected' => $selected,
            ];
        }

        return [
            'labels' => $categories,
            'distribution' => $distribution,
            'selected_distribution' => $selectedDistribution,
            'table' => $table,
        ];
    }

    private function getMonthlyTrend(array $filters): array
    {
        $query = Application::query();
        $this->applyApplicationFilters($query, $filters);

        $rows = $query
            ->selectRaw('YEAR(created_at) as y')
            ->selectRaw('MONTH(created_at) as m')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('y', 'm')
            ->orderBy('y')
            ->orderBy('m')
            ->get();

        $labels = [];
        $values = [];
        foreach ($rows as $row) {
            $labels[] = Carbon::createFromDate((int) $row->y, (int) $row->m, 1)->format('M Y');
            $values[] = (int) $row->total;
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    private function getStatusDistribution(array $filters): array
    {
        $query = Application::query();
        $this->applyApplicationFilters($query, $filters);

        $rows = $query
            ->select('status')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('status')
            ->orderBy('status')
            ->get();

        return [
            'labels' => $rows->pluck('status')->values(),
            'values' => $rows->pluck('total')->map(fn ($v) => (int) $v)->values(),
        ];
    }

    private function applyApplicationFilters(Builder $query, array $filters, bool $skipCategoryFilter = false): void
    {
        if (!empty($filters['post_id'])) {
            $query->where('post_id', $filters['post_id']);
        }

        if (!$skipCategoryFilter && !empty($filters['category'])) {
            $query->whereRaw('UPPER(category) = ?', [$filters['category']]);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }
    }

    private function normalizeFilters(array $filters): array
    {
        return [
            'post_id' => $filters['post_id'] ?? null,
            'date_from' => $filters['date_from'] ?? null,
            'date_to' => $filters['date_to'] ?? null,
            'category' => !empty($filters['category']) ? strtoupper((string) $filters['category']) : null,
        ];
    }

    private function postLabelSqlExpression(): string
    {
        $parts = [];
        foreach (['name', 'title', 'post_name', 'code'] as $column) {
            if (Schema::hasColumn('posts', $column)) {
                $parts[] = "posts.$column";
            }
        }

        if ($parts === []) {
            return "CONCAT('Post #', posts.id)";
        }

        if (count($parts) === 1) {
            return $parts[0];
        }

        return 'COALESCE(' . implode(', ', $parts) . ", CONCAT('Post #', posts.id))";
    }

    private function postSelectColumns(): array
    {
        $columns = ['id'];
        foreach (['name', 'code', 'title', 'post_name'] as $column) {
            if (Schema::hasColumn('posts', $column)) {
                $columns[] = $column;
            }
        }

        return $columns;
    }

    private function postLabelFromModel($post): string
    {
        if (!$post) {
            return '-';
        }

        return (string) (
            $post->name
            ?? $post->title
            ?? $post->post_name
            ?? $post->code
            ?? ('Post #' . $post->id)
        );
    }
}
