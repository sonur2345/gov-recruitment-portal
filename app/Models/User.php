<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'father_name',
        'mother_name',
        'dob',
        'gender',
        'category',
        'marital_status',
        'nationality',
        'mobile',
        'correspondence_address',
        'permanent_address',
        'aadhaar_number',
        'id_proof_path',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function dashboardRouteName(): string
    {
        return $this->hasAnyRole(['SuperAdmin', 'Admin']) ? 'admin.dashboard' : 'dashboard';
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'dob' => 'date',
            'aadhaar_number' => 'encrypted',
            'password' => 'hashed',
        ];
    }
}
