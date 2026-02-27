<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreNotificationRequest;
use App\Http\Requests\Admin\UpdateNotificationRequest;
use App\Models\Notification;
use App\Models\NotificationRevision;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function __construct(
        private readonly AuditService $auditService
    ) {
    }

    public function index(): View
    {
        $notifications = Notification::query()->latest()->paginate(15);

        return view('admin.notifications.index', compact('notifications'));
    }

    public function create(): View
    {
        return view('admin.notifications.create');
    }

    public function store(StoreNotificationRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $pdfPath = null;

        if ($request->hasFile('pdf')) {
            $pdfPath = $request->file('pdf')->store('notifications', 'local');
        }

        $notification = Notification::create([
            'advertisement_no' => $data['advertisement_no'] ?? null,
            'title' => $data['title'],
            'description' => $data['description'],
            'postal_address' => $data['postal_address'] ?? null,
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'last_date_time' => $data['last_date_time'] ?? null,
            'dd_payee_text' => $data['dd_payee_text'] ?? null,
            'fee_last_date' => $data['fee_last_date'] ?? $data['end_date'],
            'exam_date' => $data['exam_date'] ?? null,
            'helpdesk_phone' => $data['helpdesk_phone'] ?? null,
            'helpdesk_email' => $data['helpdesk_email'] ?? null,
            'status' => $data['status'],
            'version' => 1,
            'pdf_path' => $pdfPath,
        ]);

        $this->auditService->logModel(
            action: 'notification_created',
            model: $notification,
            newData: $notification->only([
                'advertisement_no',
                'title',
                'start_date',
                'end_date',
                'postal_address',
                'last_date_time',
                'dd_payee_text',
                'fee_last_date',
                'exam_date',
                'helpdesk_phone',
                'helpdesk_email',
                'status',
                'version',
                'pdf_path',
            ]),
            request: $request,
            userId: $request->user()->id
        );

        return redirect()
            ->route('admin.notifications.index')
            ->with('success', 'Notification created successfully.');
    }

    public function show(Notification $notification): View
    {
        return view('admin.notifications.show', compact('notification'));
    }

    public function edit(Notification $notification): View|RedirectResponse
    {
        if ($notification->status === 'closed') {
            return redirect()
                ->route('admin.notifications.index')
                ->with('error', 'Closed notifications cannot be edited.');
        }

        return view('admin.notifications.edit', compact('notification'));
    }

    public function update(UpdateNotificationRequest $request, Notification $notification): RedirectResponse
    {
        if ($notification->status === 'closed') {
            return redirect()
                ->route('admin.notifications.index')
                ->with('error', 'Closed notifications cannot be edited.');
        }

        $data = $request->validated();
        $oldData = $notification->only([
            'advertisement_no',
            'title',
            'start_date',
            'end_date',
            'postal_address',
            'last_date_time',
            'dd_payee_text',
            'fee_last_date',
            'exam_date',
            'helpdesk_phone',
            'helpdesk_email',
            'status',
            'version',
            'pdf_path',
        ]);

        NotificationRevision::create([
            'notification_id' => $notification->id,
            'version' => $notification->version,
            'data' => $oldData,
            'pdf_path' => $notification->pdf_path,
            'created_by' => $request->user()->id,
        ]);

        if ($request->hasFile('pdf')) {
            $this->deleteFile($notification->pdf_path);
            $data['pdf_path'] = $request->file('pdf')->store('notifications', 'local');
        }

        unset($data['pdf']);
        $data['version'] = $notification->version + 1;
        $notification->update($data);

        $this->auditService->logModel(
            action: 'notification_updated',
            model: $notification,
            oldData: $oldData,
            newData: $notification->only([
                'advertisement_no',
                'title',
                'start_date',
                'end_date',
                'postal_address',
                'last_date_time',
                'dd_payee_text',
                'fee_last_date',
                'exam_date',
                'helpdesk_phone',
                'helpdesk_email',
                'status',
                'version',
                'pdf_path',
            ]),
            request: $request,
            userId: $request->user()->id
        );

        return redirect()
            ->route('admin.notifications.index')
            ->with('success', 'Notification updated successfully.');
    }

    public function destroy(\Illuminate\Http\Request $request, Notification $notification): RedirectResponse
    {
        if ($notification->status === 'closed') {
            return redirect()
                ->route('admin.notifications.index')
                ->with('error', 'Closed notifications cannot be edited.');
        }

        $oldData = $notification->only([
            'advertisement_no',
            'title',
            'start_date',
            'end_date',
            'postal_address',
            'last_date_time',
            'dd_payee_text',
            'fee_last_date',
            'exam_date',
            'helpdesk_phone',
            'helpdesk_email',
            'status',
            'version',
            'pdf_path',
        ]);

        NotificationRevision::create([
            'notification_id' => $notification->id,
            'version' => $notification->version,
            'data' => $oldData,
            'pdf_path' => $notification->pdf_path,
            'created_by' => $request->user()->id,
        ]);

        $this->deleteFile($notification->pdf_path);

        $notification->delete();

        $this->auditService->log(
            action: 'notification_deleted',
            modelType: Notification::class,
            modelId: $notification->id,
            oldData: $oldData,
            request: $request,
            userId: $request->user()->id
        );

        return redirect()
            ->route('admin.notifications.index')
            ->with('success', 'Notification deleted successfully.');
    }

    private function deleteFile(?string $path): void
    {
        if (!$path) {
            return;
        }

        foreach (['local', 'public'] as $disk) {
            if (Storage::disk($disk)->exists($path)) {
                Storage::disk($disk)->delete($path);
                break;
            }
        }
    }
}
