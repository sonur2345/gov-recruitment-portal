<?php

namespace App\Http\Controllers;

use App\Models\ApplicationDocument;
use App\Models\GrievanceDocument;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileDownloadController extends Controller
{
    public function notificationPdf(Notification $notification): StreamedResponse
    {
        abort_unless($notification->pdf_path, 404, 'Notification PDF not found.');

        return $this->fileResponse(
            path: $notification->pdf_path,
            filename: basename($notification->pdf_path)
        );
    }

    public function applicationDocument(Request $request, ApplicationDocument $document): StreamedResponse
    {
        $document->loadMissing('application');

        $user = $request->user();
        $isOwner = $document->application?->user_id === $user?->id;
        $hasStaffPermission = $user?->canAny([
            'verify_dd',
            'scrutinize_applications',
            'shortlist_candidates',
            'evaluate_skill_test',
            'verify_documents',
            'generate_appointment',
            'view_audit_logs',
        ]) ?? false;

        abort_unless($isOwner || $hasStaffPermission, 403);

        return $this->fileResponse(
            path: $document->file_path,
            filename: $document->original_name
        );
    }

    public function grievanceDocument(Request $request, GrievanceDocument $document): StreamedResponse
    {
        $document->loadMissing('grievance');

        $user = $request->user();
        $isOwner = $document->grievance?->user_id === $user?->id;
        $hasStaffPermission = $user?->can('manage_grievances') ?? false;

        abort_unless($isOwner || $hasStaffPermission, 403);

        return $this->fileResponse(
            path: $document->file_path,
            filename: $document->original_name
        );
    }

    private function fileResponse(string $path, ?string $filename = null): StreamedResponse
    {
        $disk = $this->resolveDisk($path);
        abort_unless($disk !== null, 404, 'File not found.');

        return Storage::disk($disk)->download($path, $filename ?: basename($path));
    }

    private function resolveDisk(string $path): ?string
    {
        if (Storage::disk('local')->exists($path)) {
            return 'local';
        }

        if (Storage::disk('public')->exists($path)) {
            return 'public';
        }

        return null;
    }
}
