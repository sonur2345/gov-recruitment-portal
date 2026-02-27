<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GrievanceDocument extends Model
{
    protected $fillable = [
        'grievance_id',
        'file_path',
        'original_name',
        'mime_type',
        'size',
        'uploaded_by',
    ];

    public function grievance()
    {
        return $this->belongsTo(Grievance::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
