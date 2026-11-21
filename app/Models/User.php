<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['first_name', 'last_name', 'email', 'phone', 'company_name', 'password'])]
#[Hidden(['password', 'remember_token', 'profile_photo_path'])]
class User extends Authenticatable implements MustVerifyEmail
{
    public function projects() { return $this->hasMany(Project::class); }

    public function repairRequests() { return $this->hasMany(RepairRequest::class); }

    use HasFactory, Notifiable;

    protected $attributes = [
        'role' => 'customer',
        'status' => 'active',
    ];

    protected function casts(): array
    {
        return ['email_verified_at' => 'datetime', 'last_login_at' => 'datetime', 'password' => 'hashed'];
    }

    public function getNameAttribute(): string
    {
        return trim($this->first_name.' '.$this->last_name);
    }

    public function dashboardRoute(): string
    {
        return $this->role === 'customer' ? 'dashboard' : 'admin.dashboard';
    }
}
