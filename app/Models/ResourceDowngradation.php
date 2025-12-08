<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResourceDowngradation extends Model
{
    protected $fillable = [
        'customer_id',
        'status_id',
        'activation_date',
        'inactivation_date',
        'task_status_id',
        'inserted_by',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function status()
    {
        return $this->belongsTo(CustomerStatus::class, 'status_id');
    }

    public function taskStatus()
    {
        return $this->belongsTo(TaskStatus::class);
    }

    public function insertedBy()
    {
        return $this->belongsTo(User::class, 'inserted_by');
    }

    public function details()
    {
        return $this->hasMany(ResourceDowngradationDetail::class);
    }

    public function task()
    {
        return $this->hasOne(Task::class);
    }
}
