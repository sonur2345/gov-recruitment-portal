<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ApplicationStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreDocumentVerificationRequest;
use App\Models\Application;
use App\Models\DocumentVerification;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class DocumentVerificationController extends Controller
{
    public function __construct(
        private readonly AuditService $auditService
    ) {
    }

    public function index(): View
    {
        $postColumns = $this->postSelectColumns();

        $applications = Application::query()
            ->where('status', ApplicationStatus::Selected->value)
            ->with([
                'user:id,name,email',
                'post' => fn ($q) => $q->select($postColumns),
            ])
            ->latest('id')
            ->paginate(20);

        return view('admin.document_verifications.index', compact('applications'));
    }

    public function show(Application $application): View
    {
        $this->guardCandidateIsSelected($application);
        $this->guardFinalDecisionNotMade($application);

        $postColumns = $this->postSelectColumns();

        $application->load([
            'user:id,name,email',
            'post' => fn ($q) => $q->select($postColumns),
            'documents',
            'documentVerification.committeeMember:id,name,email',
        ]);

        return view('admin.document_verifications.show', compact('application'));
    }

    public function store(StoreDocumentVerificationRequest $request, Application $application): RedirectResponse
    {
        $this->guardCandidateIsSelected($application);
        $this->guardFinalDecisionNotMade($application);

        $validated = $request->validated();

        DB::transaction(function () use ($request, $application, $validated): void {
            $existingVerification = $application->documentVerification;
            $oldApplicationStatus = $application->status;

            $verification = DocumentVerification::query()->updateOrCreate(
                ['application_id' => $application->id],
                [
                    'committee_member_id' => $request->user()->id,
                    'status' => $validated['status'],
                    'remark' => $validated['remark'] ?? null,
                    'checklist' => $validated['checklist'] ?? null,
                ]
            );

            $updatedApplicationStatus = match ($validated['status']) {
                'verified' => ApplicationStatus::FinalSelected->value,
                'provisional' => ApplicationStatus::DvPending->value,
                'rejected' => ApplicationStatus::Rejected->value,
            };

            $application->update([
                'status' => $updatedApplicationStatus,
            ]);

            $promotedApplication = null;
            if ($validated['status'] === 'rejected') {
                $waitingQuery = Application::query()
                    ->where('post_id', $application->post_id)
                    ->where('status', ApplicationStatus::Waiting->value)
                    ->lockForUpdate();

                if (Schema::hasColumn('applications', 'rank')) {
                    $waitingQuery->orderBy('rank');
                } else {
                    $waitingQuery
                        ->orderByDesc('total_marks')
                        ->orderBy('dob')
                        ->orderBy('id');
                }

                $promotedApplication = $waitingQuery->first();
                if ($promotedApplication) {
                    $promotedApplication->update([
                        'status' => ApplicationStatus::Selected->value,
                    ]);
                }
            }

            $this->auditService->logModel(
                action: 'document_verification_updated',
                model: $application,
                oldData: [
                    'application_status' => $oldApplicationStatus,
                    'document_verification' => $existingVerification?->only([
                        'status',
                        'remark',
                        'committee_member_id',
                    ]),
                ],
                newData: [
                    'application_status' => $application->status,
                    'document_verification' => $verification->only([
                        'status',
                        'remark',
                        'committee_member_id',
                    ]),
                    'promoted_waiting_application_id' => $promotedApplication?->id,
                ],
                request: $request,
                userId: $request->user()->id
            );
        });

        return redirect()
            ->route('admin.document-verifications.index')
            ->with('success', 'Document verification saved successfully.');
    }

    private function guardCandidateIsSelected(Application $application): void
    {
        abort_unless(
            $application->status === ApplicationStatus::Selected->value,
            409,
            'Only selected candidates can be processed for document verification.'
        );
    }

    private function guardFinalDecisionNotMade(Application $application): void
    {
        $existingVerification = $application->documentVerification;

        abort_if(
            $existingVerification && in_array($existingVerification->status, ['verified', 'rejected'], true),
            409,
            'Final document verification decision already exists for this candidate.'
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
