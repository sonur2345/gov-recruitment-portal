<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateGrievanceRequest;
use App\Models\Grievance;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GrievanceController extends Controller
{
    public function __construct(
        private readonly AuditService $auditService
    ) {
    }

    public function index(Request $request): View
    {
        $validated = $request->validate([
            'status' => ['nullable', 'string'],
            'priority' => ['nullable', 'string'],
        ]);

        $query = Grievance::query()
            ->with(['user:id,name,email', 'application:id,application_no,post_id']);

        if (!empty($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        if (!empty($validated['priority'])) {
            $query->where('priority', $validated['priority']);
        }

        $grievances = $query->latest('id')->paginate(20)->withQueryString();

        return view('admin.grievances.index', [
            'grievances' => $grievances,
            'filters' => $validated,
        ]);
    }

    public function show(Grievance $grievance): View
    {
        $grievance->load(['user:id,name,email', 'application.post:id,name,code', 'documents', 'admin:id,name,email']);

        return view('admin.grievances.show', compact('grievance'));
    }

    public function update(UpdateGrievanceRequest $request, Grievance $grievance): RedirectResponse
    {
        $validated = $request->validated();
        $oldData = $grievance->only(['status', 'priority', 'response', 'admin_id', 'resolved_at']);

        $resolvedAt = null;
        if (in_array($validated['status'], ['resolved', 'closed'], true)) {
            $resolvedAt = now();
        }

        $grievance->update([
            'status' => $validated['status'],
            'priority' => $validated['priority'] ?? $grievance->priority,
            'response' => $validated['response'] ?? $grievance->response,
            'admin_id' => $request->user()->id,
            'resolved_at' => $resolvedAt,
        ]);

        $this->auditService->logModel(
            action: 'grievance_updated',
            model: $grievance,
            oldData: $oldData,
            newData: $grievance->only(['status', 'priority', 'response', 'admin_id', 'resolved_at']),
            request: $request,
            userId: $request->user()->id
        );

        return redirect()
            ->route('admin.grievances.show', $grievance)
            ->with('success', 'Grievance updated successfully.');
    }
}
