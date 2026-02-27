<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Scrutiny extends Model
{
    protected $fillable = [
        'application_id',
        'scrutiny_officer_id',
        'status',
        'remark',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function scrutinyOfficer()
    {
        return $this->belongsTo(User::class, 'scrutiny_officer_id');
    }
}
