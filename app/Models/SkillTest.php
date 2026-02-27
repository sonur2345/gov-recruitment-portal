<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SkillTest extends Model
{
    protected $fillable = [
        'application_id',
        'evaluator_id',
        'marks',
        'is_absent',
        'qualified',
        'remark',
    ];

    protected $casts = [
        'marks' => 'decimal:2',
        'is_absent' => 'boolean',
        'qualified' => 'boolean',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }
}
