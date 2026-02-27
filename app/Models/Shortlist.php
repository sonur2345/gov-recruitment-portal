<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shortlist extends Model
{
    protected $fillable = [
        'post_id',
        'application_id',
        'rank',
    ];

    protected $casts = [
        'rank' => 'integer',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function application()
    {
        return $this->belongsTo(Application::class);
    }
}
