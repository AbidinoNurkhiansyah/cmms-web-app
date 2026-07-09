<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'jid_no',
        'username',
        'position',
        'team',
        'jobdesc',
        'rank',
        'repair',
        'status',
        'photo',
        'role',
        'target_new',
        'target_last',
        'target_month_new',
        'target_month_last',
    ];

    /**
     * Role definitions
     */
    const ROLE_OPERATOR = 'Operator (Produksi)';
    const ROLE_TECHNICIAN = 'Maintenance Technician';
    const ROLE_PLANNER = 'Maintenance Planner';
    const ROLE_SUPERVISOR = 'Maintenance Supervisor';
    const ROLE_MANAGER = 'Manager';

    /**
     * Check if user has a specific role
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Check if user has any of the given roles
     */
    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role, $roles);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function overtimes()
    {
        return $this->hasMany(Overtime::class);
    }
}
