<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grievance extends Model
{
    protected $fillable = [
        'user_id',
        'application_id',
        'subject',
        'description',
        'status',
        'priority',
        'admin_id',
        'response',
        'sla_due_at',
        'resolved_at',
    ];

    protected $casts = [
        'sla_due_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function documents()
    {
        return $this->hasMany(GrievanceDocument::class);
    }
}
