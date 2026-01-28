<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * App User model
 *
 * Helpful IDE annotations for dynamic role helpers and Eloquent methods.
 *
 * @mixin \Eloquent
 *
 * @method bool isAdmin()
 * @method bool isProKam()
 * @method bool isKam()
 * @method bool isProTech()
 * @method bool isTech()
 * @method bool isProTechOrTech()
 * @method bool isManagement()
 * @method bool isBill()
 */
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
        'username',
        'email',
        'password',
        'role_id',
        'department_id',
        'first_login_at',
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

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'first_login_at' => 'datetime',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function department()
    {
        return $this->belongsTo(UserDepartment::class, 'department_id');
    }

    public function isAdmin()
    {
        return $this->role && $this->role->role_name === 'admin';
    }

    public function isProKam()
    {
        return $this->role && $this->role->role_name === 'pro-kam';
    }

    public function isKam()
    {
        return $this->role && $this->role->role_name === 'kam';
    }

    public function isProTech()
    {
        return $this->role && $this->role->role_name === 'pro-tech';
    }

    public function isTech()
    {
        return $this->role && $this->role->role_name === 'tech';
    }

    public function isProTechOrTech()
    {
        return $this->role && in_array($this->role->role_name, ['pro-tech', 'tech']);
    }

    public function isManagement()
    {
        return $this->role && $this->role->role_name === 'management';
    }

    public function isBill()
    {
        return $this->role && $this->role->role_name === 'bill';
    }

    public function submittedCustomers()
    {
        return $this->hasMany(Customer::class, 'submitted_by');
    }

    public function processedCustomers()
    {
        return $this->hasMany(Customer::class, 'processed_by');
    }

    public function assignedTasks()
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    public function tasksAssigned()
    {
        return $this->hasMany(Task::class, 'assigned_by');
    }
}
