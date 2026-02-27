<?php

namespace App\Services;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\AppointmentOrder;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class AppointmentOrderService
{
    public function __construct(
        private readonly AuditService $auditService
    ) {
    }

    public function generate(
        Application $application,
        int $adminId,
        string $officeAddress,
        ?string $signatureName,
        string $ipAddress,
        ?string $userAgent = null
    ): AppointmentOrder {
        $pdfPath = null;

        DB::beginTransaction();
        try {
            $application->loadMissing(['user', 'post', 'appointmentOrder']);

            if ($application->status !== ApplicationStatus::FinalSelected->value) {
                throw new RuntimeException('Appointment order can be generated only for final_selected applications.');
            }

            $existing = AppointmentOrder::query()
                ->where('application_id', $application->id)
                ->lockForUpdate()
                ->first();

            if ($existing) {
                throw new RuntimeException('Appointment order already generated for this application.');
            }

            $year = now()->year;
            $postCode = $this->resolvePostCode($application);
            $orderNumber = $this->nextOrderNumber($postCode, $year);
            $referenceNumber = $this->nextReferenceNumber($year);

            $issueDate = now()->toDateString();
            $joiningDeadline = now()->addDays(30)->toDateString();

            $viewData = [
                'orderNumber' => $orderNumber,
                'referenceNumber' => $referenceNumber,
                'issueDate' => $issueDate,
                'joiningDeadline' => $joiningDeadline,
                'candidateName' => $application->user?->name ?? '-',
                'fatherName' => $application->father_name ?? '-',
                'postName' => $this->resolvePostName($application),
                'category' => $application->category ?? '-',
                'meritRank' => $application->rank ?? '-',
                'officeAddress' => $officeAddress,
                'signatureName' => $signatureName ?: 'Authorized Signatory',
            ];

            $pdfContent = Pdf::loadView('admin.appointment_orders.pdf', $viewData)
                ->setPaper('a4')
                ->output();

            $filename = str_replace('/', '-', $orderNumber) . '.pdf';
            $pdfPath = 'appointment-orders/' . $filename;
            Storage::disk('local')->put($pdfPath, $pdfContent);

            $order = AppointmentOrder::create([
                'application_id' => $application->id,
                'order_number' => $orderNumber,
                'reference_number' => $referenceNumber,
                'issue_date' => $issueDate,
                'joining_deadline' => $joiningDeadline,
                'office_address' => $officeAddress,
                'signature_name' => $signatureName,
                'pdf_path' => $pdfPath,
                'generated_by' => $adminId,
            ]);

            $this->auditService->logModel(
                action: 'appointment_order_generated',
                model: $order,
                newData: [
                    'application_id' => $application->id,
                    'order_number' => $orderNumber,
                    'reference_number' => $referenceNumber,
                    'joining_deadline' => $joiningDeadline,
                ],
                userId: $adminId,
                ipAddress: $ipAddress,
                userAgent: $userAgent
            );

            DB::commit();
            return $order;
        } catch (Throwable $e) {
            DB::rollBack();
            if ($pdfPath && Storage::disk('local')->exists($pdfPath)) {
                Storage::disk('local')->delete($pdfPath);
            }
            if ($pdfPath && Storage::disk('public')->exists($pdfPath)) {
                Storage::disk('public')->delete($pdfPath);
            }
            throw $e;
        }
    }

    private function nextOrderNumber(string $postCode, int $year): string
    {
        $prefix = "APP/{$postCode}/{$year}/";
        $last = AppointmentOrder::query()
            ->where('order_number', 'like', $prefix . '%')
            ->lockForUpdate()
            ->orderByDesc('id')
            ->value('order_number');

        $next = $last ? ((int) substr($last, -4)) + 1 : 1;

        return $prefix . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }

    private function nextReferenceNumber(int $year): string
    {
        $prefix = "REF/{$year}/";
        $last = AppointmentOrder::query()
            ->where('reference_number', 'like', $prefix . '%')
            ->lockForUpdate()
            ->orderByDesc('id')
            ->value('reference_number');

        $next = $last ? ((int) substr($last, -4)) + 1 : 1;

        return $prefix . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }

    private function resolvePostCode(Application $application): string
    {
        $rawCode = (string) ($application->post?->code ?? 'POST');
        $cleanCode = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $rawCode) ?: 'POST');

        return $cleanCode;
    }

    private function resolvePostName(Application $application): string
    {
        return (string) (
            $application->post?->name
            ?? $application->post?->title
            ?? $application->post?->post_name
            ?? 'Post'
        );
    }
}
