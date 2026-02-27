<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ApplicationStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ScrutinyDecisionRequest;
use App\Models\Application;
use App\Models\Scrutiny;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class ScrutinyController extends Controller
{
    public function __construct(
        private readonly AuditService $auditService
    ) {
    }

    public function index(Request $request): JsonResponse|View
    {
        $postColumns = $this->postSelectColumns();

        $applications = Application::query()
            ->where('status', ApplicationStatus::DdVerified->value)
            ->with([
                'user:id,name,email',
                'post' => fn ($q) => $q->select($postColumns),
            ])
            ->latest('id')
            ->paginate(20);

        if ($request->expectsJson()) {
            return response()->json($applications);
        }

        return view('admin.scrutiny.index', compact('applications'));
    }

    public function show(Request $request, Application $application): JsonResponse|View
    {
        $postColumns = $this->postSelectColumns();

        $application->load([
            'user:id,name,email',
            'post' => fn ($q) => $q->select($postColumns),
            'educations',
            'experiences',
            'demandDraft',
            'documents',
            'scrutiny:id,application_id,scrutiny_officer_id,status,remark,created_at,updated_at',
            'scrutiny.scrutinyOfficer:id,name,email',
        ]);

        $payload = $application->toArray();
        $payload['documents'] = array_values(array_filter([
            $application->demandDraft ? [
                'type' => 'demand_draft',
                'data' => $application->demandDraft->toArray(),
            ] : null,
        ]));

        if ($request->expectsJson()) {
            return response()->json($payload);
        }

        return view('admin.scrutiny.show', [
            'application' => $application,
            'scrutiny' => $application->scrutiny,
        ]);
    }

    public function update(ScrutinyDecisionRequest $request, Application $application): JsonResponse|RedirectResponse
    {
        $this->guardCanBeScrutinized($application);

        $validated = $request->validated();
        $mappedStatus = $this->mapDecisionToStatus($validated['decision']);
        $existingScrutiny = Scrutiny::query()->where('application_id', $application->id)->first();
        $educationPercentage = round((float) ($application->educations()->avg('percentage') ?? 0), 2);
        $experienceMonths = (int) $application->experiences()->sum('total_months');
        $experienceMarks = min(20, round(($experienceMonths / 12) * 5, 2));

        $oldData = [
            'status' => $application->status,
            'scrutiny' => $existingScrutiny?->only(['status', 'remark', 'scrutiny_officer_id']),
        ];

        DB::transaction(function () use (
            $application,
            $request,
            $validated,
            $mappedStatus,
            $oldData,
            $existingScrutiny,
            $educationPercentage,
            $experienceMarks
        ): void {
            $scrutiny = Scrutiny::query()->updateOrCreate(
                ['application_id' => $application->id],
                [
                    'scrutiny_officer_id' => $request->user()->id,
                    'status' => $validated['decision'],
                    'remark' => $validated['remark'] ?? null,
                ]
            );

            $updatePayload = [
                'status' => $mappedStatus,
            ];

            if ($mappedStatus === ApplicationStatus::Eligible->value) {
                $updatePayload['education_percentage'] = $educationPercentage;
                $updatePayload['experience_marks'] = $experienceMarks;
            }

            $application->update($updatePayload);

            $this->auditService->logModel(
                action: 'scrutiny_decision_updated',
                model: $application,
                oldData: $oldData,
                newData: [
                    'status' => $mappedStatus,
                    'education_percentage' => $application->education_percentage,
                    'experience_marks' => $application->experience_marks,
                    'scrutiny' => $scrutiny->only(['status', 'remark', 'scrutiny_officer_id']),
                ],
                request: $request,
                userId: $request->user()->id
            );
        });

        if ($request->expectsJson()) {
            $postColumns = $this->postSelectColumns();
            return response()->json([
                'message' => 'Scrutiny decision saved successfully.',
                'application' => $application->fresh([
                    'user:id,name,email',
                    'post' => fn ($q) => $q->select($postColumns),
                    'scrutiny.scrutinyOfficer:id,name,email',
                ]),
            ]);
        }

        return redirect()
            ->route('admin.scrutiny.show', $application)
            ->with('success', 'Scrutiny decision saved successfully.');
    }

    private function mapDecisionToStatus(string $decision): string
    {
        return match ($decision) {
            'eligible' => ApplicationStatus::Eligible->value,
            'not_eligible' => ApplicationStatus::Rejected->value,
            'pending' => ApplicationStatus::UnderScrutiny->value,
        };
    }

    private function guardCanBeScrutinized(Application $application): void
    {
        $scrutiny = Scrutiny::query()->where('application_id', $application->id)->first();

        $allowedStatuses = [
            ApplicationStatus::DdVerified->value,
            ApplicationStatus::UnderScrutiny->value,
        ];
        abort_unless(in_array($application->status, $allowedStatuses, true), 409, 'Application cannot be scrutinized in current status.');

        $isFinalByScrutiny = in_array($scrutiny?->status, ['eligible', 'not_eligible'], true);
        $isFinalByApplication = in_array($application->status, [
            ApplicationStatus::Eligible->value,
            ApplicationStatus::Rejected->value,
        ], true);

        abort_if($isFinalByScrutiny || $isFinalByApplication, 409, 'Final scrutiny decision already made and cannot be edited.');
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
