<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePostRequest;
use App\Http\Requests\Admin\UpdatePostRequest;
use App\Models\Notification;
use App\Models\Post;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class PostController extends Controller
{
    public function __construct(
        private readonly AuditService $auditService
    ) {
    }

    public function index(): View
    {
        $posts = Post::query()
            ->with('notification:id,title')
            ->latest()
            ->paginate(15);

        return view('admin.posts.index', compact('posts'));
    }

    public function create(): View
    {
        $notificationColumns = ['id', 'title'];
        if (Schema::hasColumn('notifications', 'advertisement_no')) {
            $notificationColumns[] = 'advertisement_no';
        }

        $notifications = Notification::query()->orderByDesc('id')->get($notificationColumns);

        return view('admin.posts.create', compact('notifications'));
    }

    public function store(StorePostRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['category_breakup'] = json_decode($validated['category_breakup'], true, 512, JSON_THROW_ON_ERROR);
        $validated['title'] = $validated['name'];
        $validated['total_posts'] = $validated['total_vacancies'];

        $post = Post::create($validated);

        $this->auditService->logModel(
            action: 'post_created',
            model: $post,
            newData: $post->only([
                'notification_id',
                'name',
                'code',
                'total_vacancies',
                'age_min',
                'age_max',
                'pay_level',
                'application_fee_general',
                'application_fee_reserved',
                'exam_date',
                'experience_required',
                'skill_test_required',
                'weight_education',
                'weight_skill',
                'weight_experience',
            ]),
            request: $request,
            userId: $request->user()->id
        );

        return redirect()
            ->route('admin.posts.index')
            ->with('success', 'Post created successfully.');
    }

    public function show(Post $post): View
    {
        $post->load('notification:id,title');

        return view('admin.posts.show', compact('post'));
    }

    public function edit(Post $post): View
    {
        $notificationColumns = ['id', 'title'];
        if (Schema::hasColumn('notifications', 'advertisement_no')) {
            $notificationColumns[] = 'advertisement_no';
        }

        $notifications = Notification::query()->orderByDesc('id')->get($notificationColumns);

        return view('admin.posts.edit', compact('post', 'notifications'));
    }

    public function update(UpdatePostRequest $request, Post $post): RedirectResponse
    {
        $validated = $request->validated();
        $validated['category_breakup'] = json_decode($validated['category_breakup'], true, 512, JSON_THROW_ON_ERROR);
        $validated['title'] = $validated['name'];
        $validated['total_posts'] = $validated['total_vacancies'];
        $oldData = $post->only([
            'notification_id',
            'name',
            'code',
            'total_vacancies',
            'age_min',
            'age_max',
            'pay_level',
            'application_fee_general',
            'application_fee_reserved',
            'exam_date',
            'experience_required',
            'skill_test_required',
            'weight_education',
            'weight_skill',
            'weight_experience',
        ]);

        $post->update($validated);

        $this->auditService->logModel(
            action: 'post_updated',
            model: $post,
            oldData: $oldData,
            newData: $post->only([
                'notification_id',
                'name',
                'code',
                'total_vacancies',
                'age_min',
                'age_max',
                'pay_level',
                'application_fee_general',
                'application_fee_reserved',
                'exam_date',
                'experience_required',
                'skill_test_required',
                'weight_education',
                'weight_skill',
                'weight_experience',
            ]),
            request: $request,
            userId: $request->user()->id
        );

        return redirect()
            ->route('admin.posts.index')
            ->with('success', 'Post updated successfully.');
    }

    public function destroy(\Illuminate\Http\Request $request, Post $post): RedirectResponse
    {
        $oldData = $post->only([
            'notification_id',
            'name',
            'code',
            'total_vacancies',
            'age_min',
            'age_max',
            'pay_level',
            'application_fee_general',
            'application_fee_reserved',
            'exam_date',
            'experience_required',
            'skill_test_required',
            'weight_education',
            'weight_skill',
            'weight_experience',
        ]);

        $post->delete();

        $this->auditService->log(
            action: 'post_deleted',
            modelType: Post::class,
            modelId: $post->id,
            oldData: $oldData,
            request: $request,
            userId: $request->user()->id
        );

        return redirect()
            ->route('admin.posts.index')
            ->with('success', 'Post deleted successfully.');
    }
}
