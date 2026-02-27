<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeritGeneration extends Model
{
    protected $fillable = [
        'post_id',
        'vacancies',
        'qualified_count',
        'selected_count',
        'waiting_valid_until',
    ];

    protected $casts = [
        'waiting_valid_until' => 'date',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
