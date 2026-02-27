<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ApplicationStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GenerateAppointmentOrderRequest;
use App\Models\Application;
use App\Models\AppointmentOrder;
use App\Services\AppointmentOrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use RuntimeException;

class AppointmentOrderController extends Controller
{
    public function index(): View
    {
        $postColumns = $this->postSelectColumns();

        $applications = Application::query()
            ->where('status', ApplicationStatus::FinalSelected->value)
            ->with([
                'user:id,name,email',
                'post' => fn ($q) => $q->select($postColumns),
                'appointmentOrder:id,application_id,order_number,reference_number,pdf_path,joining_deadline',
            ])
            ->latest('id')
            ->paginate(20);

        return view('admin.appointment_orders.index', compact('applications'));
    }

    public function generate(
        GenerateAppointmentOrderRequest $request,
        Application $application,
        AppointmentOrderService $service
    ): RedirectResponse {
        $validated = $request->validated();

        try {
            $service->generate(
                $application,
                $request->user()->id,
                $validated['office_address'],
                $validated['signature_name'] ?? null,
                (string) $request->ip(),
                (string) $request->userAgent()
            );
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Appointment order generated successfully.');
    }

    public function download(AppointmentOrder $appointmentOrder)
    {
        $path = $appointmentOrder->pdf_path;
        abort_unless($path, 404, 'Appointment PDF not found.');

        $disk = Storage::disk('local')->exists($path) ? 'local' : (Storage::disk('public')->exists($path) ? 'public' : null);
        abort_unless($disk !== null, 404, 'Appointment PDF not found.');

        return Storage::disk($disk)->download($path, basename($path));
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
