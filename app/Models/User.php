<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Scout\Searchable;
use Spatie\Permission\Traits\HasRoles;
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, Searchable, SoftDeletes, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'line_name',
        'password',
        'jid_no',
        'username',
        'position',
        'team',
        'jobdesc',
        'status',
        'photo',
        'is_admin',
        'role',
        'target_new',
        'target_last',
        'target_month_new',
        'target_month_last',
    ];

    /**
     * Role definitions
     */
    const ROLE_TECHNICIAN = 'Maintenance Technician';
    const ROLE_PLANNER = 'Maintenance Planner';
    const ROLE_SUPERVISOR = 'Maintenance Supervisor';
    const ROLE_MANAGER = 'Manager';


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
     * Get the indexable data array for the model.
     *
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'username' => $this->username,
            'jid_no' => $this->jid_no,
            'position' => $this->position,
            'team' => $this->team,
            'jobdesc' => $this->jobdesc,
            'status' => $this->status,
        ];
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
            'password' => 'hashed',
        ];
    }

    public function overtimes()
    {
        return $this->hasMany(Overtime::class);
    }

    public function trainingSkills()
    {
        return $this->hasMany(TrainingSkill::class);
    }
}
