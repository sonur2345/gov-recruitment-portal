<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DemandDraft extends Model
{
    protected $fillable = [
        'application_id',
        'dd_number',
        'bank_name',
        'bank_branch',
        'dd_scan_path',
        'dd_date',
        'amount',
        'status',
        'remark',
        'admin_id',
    ];

    protected function casts(): array
    {
        return [
            'dd_date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
