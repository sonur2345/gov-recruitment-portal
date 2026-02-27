<?php

use App\Services\AuditService;

if (!function_exists('logAudit')) {
    function logAudit($action, $model, $oldData = null, $newData = null)
    {
        app(AuditService::class)->logModel(
            action: (string) $action,
            model: $model,
            oldData: $oldData,
            newData: $newData
        );
    }
}
