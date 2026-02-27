<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{

    protected $fillable = [
        'application_no',
        'user_id',
        'post_id',
        'category',
        'sub_reservation',
        'pwbd_status',
        'ex_serviceman',
        'dob',
        'gender',
        'father_name',
        'mobile',
        'address',
        'aadhar_number',
        'bank_account_number',
        'ifsc_code',
        'status',
        'source',
        'inward_no',
        'inward_date',
        'postal_received_at',
        'envelope_scan_path',
        'scrutiny_decision',
        'scrutiny_remark',
        'scrutiny_officer_id',
        'education_percentage',
        'experience_marks',
        'skill_marks',
        'total_marks',
        'rank',
    ];

    protected function casts(): array
    {
        return [
            'dob' => 'date',
            'inward_date' => 'date',
            'postal_received_at' => 'datetime',
            'pwbd_status' => 'boolean',
            'ex_serviceman' => 'boolean',
            'aadhar_number' => 'encrypted',
            'bank_account_number' => 'encrypted',
            'ifsc_code' => 'encrypted',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function scrutinyOfficer()
    {
        return $this->belongsTo(User::class, 'scrutiny_officer_id');
    }

    public function demandDraft()
    {
        return $this->hasOne(DemandDraft::class);
    }

    public function educations()
    {
        return $this->hasMany(Education::class);
    }

    public function experiences()
    {
        return $this->hasMany(Experience::class);
    }

    public function documents()
    {
        return $this->hasMany(ApplicationDocument::class);
    }

    public function scrutiny()
    {
        return $this->hasOne(Scrutiny::class);
    }

    public function skillTest()
    {
        return $this->hasOne(SkillTest::class);
    }

    public function documentVerification()
    {
        return $this->hasOne(DocumentVerification::class);
    }

    public function appointmentOrder()
    {
        return $this->hasOne(AppointmentOrder::class);
    }
}
