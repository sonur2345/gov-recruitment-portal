<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePostalIntakeRequest;
use App\Models\Application;
use App\Models\Post;
use App\Services\AuditService;
use App\Services\PostalIntakeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use RuntimeException;

class PostalIntakeController extends Controller
{
    public function __construct(
        private readonly AuditService $auditService
    ) {
    }

    public function index(Request $request): View
    {
        $validated = $request->validate([
            'post_id' => ['nullable', 'integer', 'exists:posts,id'],
            'inward_no' => ['nullable', 'string', 'max:120'],
        ]);

        $query = Application::query()
            ->where('source', 'postal')
            ->with(['post:id,name,code', 'user:id,name,email', 'demandDraft']);

        if (!empty($validated['post_id'])) {
            $query->where('post_id', $validated['post_id']);
        }

        if (!empty($validated['inward_no'])) {
            $query->where('inward_no', 'like', '%' . $validated['inward_no'] . '%');
        }

        $applications = $query->latest('id')->paginate(20)->withQueryString();

        $postColumns = ['id', 'name', 'code'];
        foreach (['title', 'post_name'] as $column) {
            if (Schema::hasColumn('posts', $column)) {
                $postColumns[] = $column;
            }
        }

        $posts = Post::query()->orderBy('id')->get(array_unique($postColumns));

        return view('admin.postal-intake.index', [
            'applications' => $applications,
            'filters' => $validated,
            'posts' => $posts,
        ]);
    }

    public function create(): View
    {
        $postColumns = ['id', 'name', 'code'];
        foreach (['title', 'post_name'] as $column) {
            if (Schema::hasColumn('posts', $column)) {
                $postColumns[] = $column;
            }
        }

        $posts = Post::query()->orderBy('id')->get(array_unique($postColumns));

        return view('admin.postal-intake.create', compact('posts'));
    }

    public function store(StorePostalIntakeRequest $request, PostalIntakeService $postalIntakeService): RedirectResponse
    {
        $validated = $request->validated();

        try {
            $application = $postalIntakeService->createFromPostal(
                $validated,
                $request->file('envelope_scan'),
                $request->file('dd_scan')
            );
        } catch (RuntimeException $exception) {
            return redirect()
                ->route('admin.postal-intake.create')
                ->withInput()
                ->with('error', $exception->getMessage());
        }

        $this->auditService->logModel(
            action: 'postal_intake_logged',
            model: $application,
            newData: [
                'application_no' => $application->application_no,
                'post_id' => $application->post_id,
                'source' => $application->source,
                'inward_no' => $application->inward_no,
            ],
            request: $request,
            userId: $request->user()->id
        );

        return redirect()
            ->route('admin.postal-intake.index')
            ->with('success', 'Postal application logged successfully.');
    }
}
