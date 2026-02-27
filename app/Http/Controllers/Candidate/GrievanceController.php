<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Http\Requests\Candidate\StoreGrievanceRequest;
use App\Models\Application;
use App\Models\Grievance;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class GrievanceController extends Controller
{
    public function index(Request $request): View
    {
        $grievances = Grievance::query()
            ->where('user_id', $request->user()->id)
            ->with('application:id,application_no,post_id')
            ->latest('id')
            ->paginate(15);

        return view('candidate.grievances.index', compact('grievances'));
    }

    public function create(Request $request): View
    {
        $applications = Application::query()
            ->where('user_id', $request->user()->id)
            ->select(['id', 'application_no', 'post_id'])
            ->with('post:id,name,code')
            ->latest('id')
            ->get();

        return view('candidate.grievances.create', compact('applications'));
    }

    public function store(StoreGrievanceRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        if (!empty($validated['application_id'])) {
            $ownsApplication = Application::query()
                ->where('id', $validated['application_id'])
                ->where('user_id', $request->user()->id)
                ->exists();

            if (! $ownsApplication) {
                return back()->withInput()->with('error', 'Invalid application selected.');
            }
        }

        $grievance = Grievance::create([
            'user_id' => $request->user()->id,
            'application_id' => $validated['application_id'] ?? null,
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'priority' => $validated['priority'] ?? 'medium',
            'status' => 'open',
            'sla_due_at' => now()->addDays(7),
        ]);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store("grievances/{$grievance->id}", 'local');
                $grievance->documents()->create([
                    'file_path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getClientMimeType(),
                    'size' => $file->getSize(),
                    'uploaded_by' => $request->user()->id,
                ]);
            }
        }

        return redirect()
            ->route('grievances.index')
            ->with('success', 'Grievance submitted successfully.');
    }

    public function show(Request $request, Grievance $grievance): View
    {
        abort_unless($grievance->user_id === $request->user()->id, 403);

        $grievance->load(['application.post', 'documents']);

        return view('candidate.grievances.show', compact('grievance'));
    }
}
