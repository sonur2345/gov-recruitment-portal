<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Http\Requests\Candidate\StoreApplicationRequest;
use App\Models\Application;
use App\Models\Post;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ApplicationController extends Controller
{
    public function __construct(
        private readonly AuditService $auditService
    ) {
    }

    public function create(Request $request, ?Post $post = null): View
    {
        $hasNotificationStatus = Schema::hasColumn('notifications', 'status');
        $notificationColumns = ['id', 'title', 'start_date', 'end_date'];
        if ($hasNotificationStatus) {
            $notificationColumns[] = 'status';
        }

        $postOrderColumn = 'id';
        foreach (['name', 'title', 'post_name'] as $candidateColumn) {
            if (Schema::hasColumn('posts', $candidateColumn)) {
                $postOrderColumn = $candidateColumn;
                break;
            }
        }

        $postsQuery = Post::query()
            ->with(['notification' => fn ($q) => $q->select($notificationColumns)])
            ->orderBy($postOrderColumn);

        if ($hasNotificationStatus) {
            $postsQuery->whereHas('notification', function ($query): void {
                $query->where('status', 'published')
                    ->whereDate('start_date', '<=', now()->toDateString())
                    ->whereDate('end_date', '>=', now()->toDateString());
            });
        }

        $posts = $postsQuery->get();

        $selectedPostId = $post?->id;
        $candidateProfile = $request->user();

        return view('application.create', compact('posts', 'selectedPostId', 'candidateProfile'));
    }

    public function preview(Request $request): View
    {
        return view('application.preview', [
            'previewData' => $request->all(),
        ]);
    }

    public function store(StoreApplicationRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $userId = $request->user()->id;

        $alreadySubmitted = Application::query()
            ->where('user_id', $userId)
            ->where('post_id', $validated['post_id'])
            ->where('status', 'submitted')
            ->exists();

        if ($alreadySubmitted) {
            throw ValidationException::withMessages([
                'post_id' => 'You have already submitted an application for this post. Editing is not allowed after submission.',
            ]);
        }

        DB::transaction(function () use ($request, $validated, $userId): void {
            $nextId = (Application::query()->lockForUpdate()->max('id') ?? 0) + 1;
            $applicationNo = 'APP-' . now()->format('Ymd') . '-' . str_pad((string) $nextId, 6, '0', STR_PAD_LEFT);

            $application = Application::create([
                'application_no' => $applicationNo,
                'user_id' => $userId,
                'post_id' => $validated['post_id'],
                'category' => $validated['category'],
                'sub_reservation' => $validated['sub_reservation'] ?? null,
                'pwbd_status' => ($validated['pwbd_status'] ?? 'no') === 'yes',
                'ex_serviceman' => ($validated['ex_serviceman'] ?? 'no') === 'yes',
                'dob' => $validated['dob'],
                'gender' => $validated['gender'],
                'father_name' => $validated['father_name'],
                'mobile' => $validated['mobile'],
                'address' => $validated['address'],
                'aadhar_number' => $validated['aadhar_number'] ?? null,
                'bank_account_number' => $validated['bank_account_number'] ?? null,
                'ifsc_code' => $validated['ifsc_code'] ?? null,
                'status' => 'submitted',
            ]);

            foreach ($validated['education'] as $educationRow) {
                $application->educations()->create($educationRow);
            }

            if (!empty($validated['experience'])) {
                foreach ($validated['experience'] as $experienceRow) {
                    $isEmptyRow = empty(array_filter($experienceRow, static fn ($value) => $value !== null && $value !== ''));
                    if ($isEmptyRow) {
                        continue;
                    }
                    $application->experiences()->create($experienceRow);
                }
            }

            $application->demandDraft()->create([
                'dd_number' => $validated['dd_number'],
                'bank_name' => $validated['bank_name'],
                'bank_branch' => $validated['bank_branch'],
                'dd_date' => $validated['dd_date'],
                'amount' => $validated['amount'],
            ]);

            $requiredDocuments = [
                'photo' => "documents.photo",
                'signature' => "documents.signature",
                'id_proof' => "documents.id_proof",
                'education_certificate' => 'education_certificate',
                'dd_copy' => 'dd_copy',
            ];

            foreach ($requiredDocuments as $docType => $inputKey) {
                $file = $request->file($inputKey);
                if (!$file) {
                    continue;
                }

                $path = $file->store("applications/{$application->application_no}", 'local');
                $application->documents()->create([
                    'document_type' => $docType,
                    'file_path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                ]);
            }

            foreach (['experience_certificate', 'reservation_certificate', 'caste_certificate'] as $optionalDocType) {
                $file = $request->file($optionalDocType);
                if (!$file) {
                    continue;
                }

                $path = $file->store("applications/{$application->application_no}", 'local');
                $application->documents()->create([
                    'document_type' => $optionalDocType,
                    'file_path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                ]);
            }

            $this->auditService->logModel(
                action: 'application_submitted',
                model: $application,
                newData: [
                    'application_no' => $application->application_no,
                    'post_id' => $application->post_id,
                    'status' => $application->status,
                ],
                request: $request,
                userId: $userId
            );
        });

        return redirect()
            ->route('dashboard')
            ->with('success', 'Application submitted successfully.');
    }
}
