<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'action' => ['nullable', 'string', 'max:255'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);

        $logs = AuditLog::query()
            ->with('user:id,name,email')
            ->when(!empty($validated['action']), function ($query) use ($validated): void {
                $query->where('action', 'like', '%' . $validated['action'] . '%');
            })
            ->when(!empty($validated['user_id']), function ($query) use ($validated): void {
                $query->where('user_id', $validated['user_id']);
            })
            ->when(!empty($validated['date_from']), function ($query) use ($validated): void {
                $query->whereDate('created_at', '>=', $validated['date_from']);
            })
            ->when(!empty($validated['date_to']), function ($query) use ($validated): void {
                $query->whereDate('created_at', '<=', $validated['date_to']);
            })
            ->latest('created_at')
            ->paginate(50)
            ->withQueryString();

        return view('admin.audit_logs.index', [
            'logs' => $logs,
            'filters' => $validated,
        ]);
    }
}
