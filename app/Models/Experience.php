<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Experience extends Model
{
    protected $fillable = [
        'application_id',
        'organization',
        'post',
        'from_date',
        'to_date',
        'total_months'
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }
}
