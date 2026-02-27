<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentVerification extends Model
{
    protected $fillable = [
        'application_id',
        'committee_member_id',
        'status',
        'remark',
        'checklist',
    ];

    protected $casts = [
        'checklist' => 'array',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function committeeMember()
    {
        return $this->belongsTo(User::class, 'committee_member_id');
    }
}
