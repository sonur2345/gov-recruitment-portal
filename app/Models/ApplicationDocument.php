<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

class ApplicationDocument extends Model
{
    protected $fillable = [
        'application_id',
        'document_type',
        'file_path',
        'original_name',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function signedDownloadUrl(int $minutes = 20): string
    {
        return URL::temporarySignedRoute(
            'files.documents.download',
            now()->addMinutes($minutes),
            ['document' => $this->id]
        );
    }
}
