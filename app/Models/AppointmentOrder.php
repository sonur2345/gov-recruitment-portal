<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

class AppointmentOrder extends Model
{
    protected $fillable = [
        'application_id',
        'order_number',
        'reference_number',
        'issue_date',
        'joining_deadline',
        'office_address',
        'signature_name',
        'pdf_path',
        'generated_by',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'joining_deadline' => 'date',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function generator()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function signedDownloadUrl(int $minutes = 20): string
    {
        return URL::temporarySignedRoute(
            'admin.appointment-orders.download',
            now()->addMinutes($minutes),
            ['appointmentOrder' => $this->id]
        );
    }
}
