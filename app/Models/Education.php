<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    protected $fillable = [
        'application_id',
        'exam',
        'board_university',
        'subject',
        'year',
        'percentage'
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }
}
