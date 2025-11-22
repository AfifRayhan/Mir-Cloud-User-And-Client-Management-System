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
        'username',
        'password',
        'role_id',
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
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function isAdmin()
    {
        return $this->role && $this->role->role_name === 'admin';
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

    public function submittedCustomers()
    {
        return $this->hasMany(Customer::class, 'submitted_by');
    }

    public function processedCustomers()
    {
        return $this->hasMany(Customer::class, 'processed_by');
    }
}
