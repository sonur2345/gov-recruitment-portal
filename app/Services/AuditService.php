<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class AuditService
{
    public function log(
        string $action,
        string $modelType,
        int|string|null $modelId = null,
        ?array $oldData = null,
        ?array $newData = null,
        ?Request $request = null,
        ?int $userId = null,
        ?string $ipAddress = null,
        ?string $userAgent = null
    ): AuditLog {
        $request ??= request();
        $userId ??= auth()->id();

        return AuditLog::create([
            'user_id' => $userId,
            'action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId ?? 0,
            'old_data' => $oldData,
            'new_data' => $newData,
            'ip_address' => $ipAddress ?? $request?->ip(),
            'user_agent' => $userAgent ?? $request?->userAgent(),
        ]);
    }

    public function logModel(
        string $action,
        Model $model,
        ?array $oldData = null,
        ?array $newData = null,
        ?Request $request = null,
        ?int $userId = null,
        ?string $ipAddress = null,
        ?string $userAgent = null
    ): AuditLog {
        return $this->log(
            action: $action,
            modelType: $model::class,
            modelId: $model->getKey(),
            oldData: $oldData,
            newData: $newData,
            request: $request,
            userId: $userId,
            ipAddress: $ipAddress,
            userAgent: $userAgent
        );
    }
}
