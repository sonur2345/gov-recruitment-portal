<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ApplicationStatus;
use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Shortlist;
use App\Services\AuditService;
use App\Services\ShortlistService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class ShortlistController extends Controller
{
    public function __construct(
        private readonly AuditService $auditService
    ) {
    }

    public function index(Request $request): View
    {
        $validated = $request->validate([
            'post_id' => ['nullable', 'integer', 'exists:posts,id'],
        ]);

        $postColumns = ['id', 'total_vacancies'];
        foreach (['name', 'title', 'post_name', 'code'] as $column) {
            if (Schema::hasColumn('posts', $column)) {
                $postColumns[] = $column;
            }
        }

        $posts = Post::query()
            ->select(array_unique($postColumns))
            ->withCount([
                'applications as eligible_count' => fn ($q) => $q->where('status', ApplicationStatus::Eligible->value),
                'applications as shortlisted_count' => fn ($q) => $q->where('status', ApplicationStatus::Shortlisted->value),
                'applications as total_applications_count',
            ])
            ->orderBy('id')
            ->get();

        $shortlistRows = collect();
        if (!empty($validated['post_id'])) {
            $applicationPostColumns = $this->applicationPostSelectColumns();

            $shortlistRows = Shortlist::query()
                ->where('post_id', $validated['post_id'])
                ->with([
                    'application.user:id,name,email',
                    'application.post' => fn ($q) => $q->select($applicationPostColumns),
                ])
                ->orderBy('rank')
                ->get();
        }

        return view('admin.shortlists.index', [
            'posts' => $posts,
            'filters' => $validated,
            'shortlistRows' => $shortlistRows,
        ]);
    }

    public function generate(Request $request, ShortlistService $shortlistService): RedirectResponse
    {
        $validated = $request->validate([
            'post_id' => ['required', 'integer', 'exists:posts,id'],
        ]);

        $post = Post::query()->findOrFail($validated['post_id']);
        $generatedCount = $shortlistService->generate($post);

        $this->auditService->log(
            action: 'shortlist_generated',
            modelType: Post::class,
            modelId: $post->id,
            newData: [
                'post_id' => $post->id,
                'generated_count' => $generatedCount,
            ],
            request: $request,
            userId: $request->user()->id
        );

        $message = $generatedCount > 0
            ? "Shortlisting completed. {$generatedCount} candidates moved to shortlisted."
            : 'No eligible candidate available for shortlisting.';

        return redirect()
            ->route('admin.shortlists.index', ['post_id' => $post->id])
            ->with('success', $message);
    }

    private function applicationPostSelectColumns(): array
    {
        $columns = ['id'];
        foreach (['name', 'title', 'post_name', 'code'] as $column) {
            if (Schema::hasColumn('posts', $column)) {
                $columns[] = $column;
            }
        }

        return array_unique($columns);
    }
}
