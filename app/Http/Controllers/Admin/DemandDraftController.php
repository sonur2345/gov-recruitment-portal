<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ApplicationStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MarkDemandDraftInvalidRequest;
use App\Models\DemandDraft;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DemandDraftController extends Controller
{
    public function __construct(
        private readonly AuditService $auditService
    ) {
    }

    public function index(): View
    {
        $pendingDDs = DemandDraft::query()
            ->where('status', 'pending')
            ->with(['application.user:id,name,email', 'application.post:id,name,code'])
            ->latest('id')
            ->paginate(20);

        return view('admin.demand_drafts.index', compact('pendingDDs'));
    }

    public function markValid(Request $request, DemandDraft $demandDraft): RedirectResponse
    {
        $this->ensurePending($demandDraft);

        DB::transaction(function () use ($request, $demandDraft): void {
            $oldDraft = $demandDraft->only(['status', 'remark', 'admin_id']);
            $oldAppStatus = $demandDraft->application?->status;

            $demandDraft->update([
                'status' => 'valid',
                'remark' => null,
                'admin_id' => $request->user()->id,
            ]);

            if ($demandDraft->application) {
                $demandDraft->application->update([
                    'status' => ApplicationStatus::DdVerified->value,
                ]);
            }

            $this->auditService->logModel(
                action: 'dd_marked_valid',
                model: $demandDraft,
                oldData: [
                    'demand_draft' => $oldDraft,
                    'application_status' => $oldAppStatus,
                ],
                newData: [
                    'demand_draft' => $demandDraft->only(['status', 'remark', 'admin_id']),
                    'application_status' => $demandDraft->application?->fresh()?->status,
                ],
                request: $request,
                userId: $request->user()->id
            );
        });

        return back()->with('success', 'Demand Draft marked as valid and application moved to DD verified.');
    }

    public function markInvalid(MarkDemandDraftInvalidRequest $request, DemandDraft $demandDraft): RedirectResponse
    {
        $this->ensurePending($demandDraft);

        DB::transaction(function () use ($request, $demandDraft): void {
            $oldDraft = $demandDraft->only(['status', 'remark', 'admin_id']);
            $oldAppStatus = $demandDraft->application?->status;

            $demandDraft->update([
                'status' => 'invalid',
                'remark' => $request->remark,
                'admin_id' => $request->user()->id,
            ]);

            if ($demandDraft->application) {
                $demandDraft->application->update([
                    'status' => ApplicationStatus::Rejected->value,
                ]);
            }

            $this->auditService->logModel(
                action: 'dd_marked_invalid',
                model: $demandDraft,
                oldData: [
                    'demand_draft' => $oldDraft,
                    'application_status' => $oldAppStatus,
                ],
                newData: [
                    'demand_draft' => $demandDraft->only(['status', 'remark', 'admin_id']),
                    'application_status' => $demandDraft->application?->fresh()?->status,
                ],
                request: $request,
                userId: $request->user()->id
            );
        });

        return back()->with('success', 'Demand Draft marked as invalid and application rejected.');
    }

    private function ensurePending(DemandDraft $demandDraft): void
    {
        abort_if($demandDraft->status !== 'pending', 409, 'This demand draft is already verified.');
    }
}
