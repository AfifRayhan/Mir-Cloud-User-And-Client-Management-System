<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'customer_id',
        'status_id',
        'activation_date',
        'allocation_type',
        'resource_upgradation_id',
        'resource_downgradation_id',
        'assigned_to',
        'assigned_by',
        'assigned_at',
        'completed_at',
    ];

    protected $casts = [
        'activation_date' => 'date',
        'assigned_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function status()
    {
        return $this->belongsTo(CustomerStatus::class, 'status_id');
    }

    public function resourceUpgradation()
    {
        return $this->belongsTo(ResourceUpgradation::class);
    }

    public function resourceDowngradation()
    {
        return $this->belongsTo(ResourceDowngradation::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    // Get the resource details based on allocation type
    public function getResourceDetailsAttribute()
    {
        if ($this->allocation_type === 'upgrade' && $this->resourceUpgradation) {
            return $this->resourceUpgradation->details;
        } elseif ($this->allocation_type === 'downgrade' && $this->resourceDowngradation) {
            return $this->resourceDowngradation->details;
        }
        return collect();
    }

    // Scope for pending tasks (not assigned)
    public function scopePending($query)
    {
        return $query->whereNull('assigned_to');
    }

    // Scope for assigned tasks
    public function scopeAssigned($query)
    {
        return $query->whereNotNull('assigned_to');
    }

    // Scope for completed tasks
    public function scopeCompleted($query)
    {
        return $query->whereNotNull('completed_at');
    }

    // Scope for tasks assigned to a specific user
    public function scopeAssignedToUser($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }
}
