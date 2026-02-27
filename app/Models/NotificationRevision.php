<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationRevision extends Model
{
    protected $fillable = [
        'notification_id',
        'version',
        'data',
        'pdf_path',
        'created_by',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function notification()
    {
        return $this->belongsTo(Notification::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
