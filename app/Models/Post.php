<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'notification_id',
        'name',
        'code',
        'total_vacancies',
        'title',
        'total_posts',
        'category_breakup',
        'age_min',
        'age_max',
        'qualification_text',
        'pay_level',
        'application_fee_general',
        'application_fee_reserved',
        'exam_date',
        'experience_required',
        'skill_test_required',
        'weight_education',
        'weight_skill',
        'weight_experience',
    ];

    protected $casts = [
        'category_breakup' => 'array',
        'application_fee_general' => 'decimal:2',
        'application_fee_reserved' => 'decimal:2',
        'exam_date' => 'date',
        'experience_required' => 'boolean',
        'skill_test_required' => 'boolean',
        'weight_education' => 'decimal:2',
        'weight_skill' => 'decimal:2',
        'weight_experience' => 'decimal:2',
    ];

    public function notification()
    {
        return $this->belongsTo(Notification::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function meritGeneration()
    {
        return $this->hasOne(MeritGeneration::class);
    }
}
