<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\URL;

class Notification extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'advertisement_no',
        'title',
        'description',
        'postal_address',
        'start_date',
        'end_date',
        'last_date_time',
        'dd_payee_text',
        'fee_last_date',
        'exam_date',
        'helpdesk_phone',
        'helpdesk_email',
        'status',
        'version',
        'pdf_path',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'last_date_time' => 'datetime',
        'fee_last_date' => 'date',
        'exam_date' => 'date',
    ];

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function revisions()
    {
        return $this->hasMany(NotificationRevision::class)->orderByDesc('version');
    }

    public function scopePublishedAndActive($query)
    {
        return $query
            ->where('status', 'published')
            ->whereDate('start_date', '<=', now()->toDateString())
            ->whereDate('end_date', '>=', now()->toDateString());
    }

    public function signedPdfUrl(int $minutes = 20): ?string
    {
        if (!$this->pdf_path) {
            return null;
        }

        return URL::temporarySignedRoute(
            'notifications.pdf.download',
            now()->addMinutes($minutes),
            ['notification' => $this->id]
        );
    }
}
