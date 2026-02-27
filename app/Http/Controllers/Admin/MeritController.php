<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ApplicationStatus;
use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Post;
use App\Services\AuditService;
use App\Services\MeritService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use RuntimeException;

class MeritController extends Controller
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
            ->with('meritGeneration:id,post_id,vacancies,qualified_count,selected_count')
            ->withCount([
                'applications as qualified_count' => fn ($q) => $q->where('status', ApplicationStatus::Qualified->value),
                'applications as selected_count' => fn ($q) => $q->whereIn('status', [ApplicationStatus::Selected->value, ApplicationStatus::FinalSelected->value]),
                'applications as waiting_count' => fn ($q) => $q->where('status', ApplicationStatus::Waiting->value),
            ])
            ->orderBy('id')
            ->get();

        $meritRows = collect();
        if (!empty($validated['post_id'])) {
            $meritRows = Application::query()
                ->where('post_id', $validated['post_id'])
                ->whereIn('status', [
                    ApplicationStatus::Selected->value,
                    ApplicationStatus::Waiting->value,
                    ApplicationStatus::FinalSelected->value,
                ])
                ->with('user:id,name,email')
                ->orderBy('rank')
                ->orderByDesc('total_marks')
                ->get();
        }

        return view('admin.merit.index', [
            'posts' => $posts,
            'filters' => $validated,
            'meritRows' => $meritRows,
        ]);
    }

    public function generate(Request $request, MeritService $meritService): RedirectResponse
    {
        $validated = $request->validate([
            'post_id' => ['required', 'integer', 'exists:posts,id'],
        ]);

        $post = Post::query()->findOrFail($validated['post_id']);

        try {
            $processedCount = $meritService->generate($post);
        } catch (RuntimeException $e) {
            return redirect()
                ->route('admin.merit.index', ['post_id' => $post->id])
                ->with('error', $e->getMessage());
        }

        $this->auditService->log(
            action: 'merit_generated',
            modelType: Post::class,
            modelId: $post->id,
            newData: [
                'post_id' => $post->id,
                'processed_count' => $processedCount,
            ],
            request: $request,
            userId: $request->user()->id
        );

        return redirect()
            ->route('admin.merit.index', ['post_id' => $post->id])
            ->with('success', "Merit generated successfully. {$processedCount} qualified candidates processed.");
    }
}
