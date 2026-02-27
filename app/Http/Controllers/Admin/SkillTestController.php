<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ApplicationStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSkillTestRequest;
use App\Models\Application;
use App\Models\SkillTest;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class SkillTestController extends Controller
{
    public function __construct(
        private readonly AuditService $auditService
    ) {
    }

    public function index(): View
    {
        $postColumns = $this->postSelectColumns();

        $applications = Application::query()
            ->where('status', ApplicationStatus::Shortlisted->value)
            ->with([
                'user:id,name,email',
                'post' => fn ($q) => $q->select($postColumns),
            ])
            ->latest('id')
            ->paginate(20);

        return view('admin.skill_tests.index', compact('applications'));
    }

    public function show(Application $application): View
    {
        $this->guardCanEvaluate($application);

        $postColumns = $this->postSelectColumns();

        $application->load([
            'user:id,name,email',
            'post' => fn ($q) => $q->select($postColumns),
            'educations',
            'experiences',
            'documents',
        ]);

        return view('admin.skill_tests.show', compact('application'));
    }

    public function store(StoreSkillTestRequest $request, Application $application): RedirectResponse
    {
        $this->guardCanEvaluate($application);

        $validated = $request->validated();
        $isAbsent = (bool) $validated['is_absent'];
        $marks = $isAbsent ? null : (float) $validated['marks'];
        $qualified = !$isAbsent && $marks >= 40;
        $newStatus = $qualified
            ? ApplicationStatus::Qualified->value
            : ApplicationStatus::Rejected->value;

        DB::transaction(function () use ($request, $application, $validated, $marks, $isAbsent, $qualified, $newStatus): void {
            $skillTest = SkillTest::create([
                'application_id' => $application->id,
                'evaluator_id' => $request->user()->id,
                'marks' => $marks,
                'is_absent' => $isAbsent,
                'qualified' => $qualified,
                'remark' => $validated['remark'] ?? null,
            ]);

            $oldData = [
                'application_status' => $application->status,
                'skill_marks' => $application->skill_marks,
            ];

            $application->update([
                'skill_marks' => $marks,
                'status' => $newStatus,
            ]);

            $this->auditService->logModel(
                action: 'skill_test_evaluated',
                model: $application,
                oldData: $oldData,
                newData: [
                    'application_status' => $application->status,
                    'skill_marks' => $application->skill_marks,
                    'skill_test' => $skillTest->only([
                        'marks',
                        'is_absent',
                        'qualified',
                        'evaluator_id',
                        'remark',
                    ]),
                ],
                request: $request,
                userId: $request->user()->id
            );
        });

        return redirect()
            ->route('admin.skill-tests.index')
            ->with('success', 'Skill test evaluation saved successfully.');
    }

    private function guardCanEvaluate(Application $application): void
    {
        abort_unless(
            $application->status === ApplicationStatus::Shortlisted->value,
            409,
            'Only shortlisted candidates can be evaluated for skill test.'
        );

        abort_if(
            SkillTest::query()->where('application_id', $application->id)->exists(),
            409,
            'Skill test already evaluated for this application.'
        );
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
}
