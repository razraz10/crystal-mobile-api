<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected string $guard_name = "passport";
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'personal_number',
        'email',
        'phone_number',
        'employee_type',
        'permission_id',
        'is_deleted',
        'remember_token'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'created_at',
        'updated_at',
        'is_deleted',
        'permission_id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function permission()
    {
        return $this->belongsTo(Permission::class);
    }

    public function mission()
    {
        return $this->hasMany(Mission::class);
    }

    public function inhibit()
    {
        return $this->hasMany(Inhibit::class);
    }

    public function market()
    {
        return $this->hasMany(Market::class);
    }
}
