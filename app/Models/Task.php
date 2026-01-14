<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'task_id',
        'customer_id',
        'status_id',
        'task_status_id',
        'activation_date',
        'allocation_type',
        'resource_upgradation_id',
        'resource_downgradation_id',
        'resource_transfer_id',
        'assigned_to',
        'assigned_by',
        'assigned_at',
        'completed_at',
        'vdc_id',
        'has_resource_conflict',
        'assignment_datetime',
        'deadline_datetime',
        'billed_at',
    ];

    protected static function booted()
    {
        static::created(function ($task) {
            $task->generateTaskId();
        });
    }

    public function generateTaskId()
    {
        $customerId = $this->customer_id;
        $platformId = $this->customer->platform_id;
        $statusId = $this->status_id;

        $typeBit = '0'; // Default to 0 (downgrade)
        if ($this->allocation_type === 'upgrade') {
            $typeBit = '1';
        } elseif ($this->allocation_type === 'transfer') {
            $typeBit = '2';
        }

        $resourceId = null;
        if ($this->allocation_type === 'upgrade') {
            $resourceId = $this->resource_upgradation_id;
        } elseif ($this->allocation_type === 'downgrade') {
            $resourceId = $this->resource_downgradation_id;
        } elseif ($this->allocation_type === 'transfer') {
            $resourceId = $this->resource_transfer_id;
        }

        $assignmentDateTime = '';
        if ($this->allocation_type === 'upgrade' && $this->resourceUpgradation) {
            $assignmentDateTime = $this->resourceUpgradation->assignment_datetime?->format('YmdHi') ?? '';
        } elseif ($this->allocation_type === 'downgrade' && $this->resourceDowngradation) {
            $assignmentDateTime = $this->resourceDowngradation->assignment_datetime?->format('YmdHi') ?? '';
        } elseif ($this->allocation_type === 'transfer' && $this->resourceTransfer) {
            $assignmentDateTime = $this->resourceTransfer->transfer_datetime?->format('YmdHi') ?? '';
        }

        $generatedId = "{$customerId}-{$platformId}-{$statusId}-{$assignmentDateTime}-{$typeBit}-{$resourceId}";
        $this->update(['task_id' => $generatedId]);

        return $generatedId;
    }

    protected $casts = [
        'activation_date' => 'date',
        'assigned_at' => 'datetime',
        'completed_at' => 'datetime',
        'assignment_datetime' => 'datetime',
        'deadline_datetime' => 'datetime',
        'billed_at' => 'datetime',
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
        return $this->belongsTo(TaskStatus::class, 'task_status_id');
    }

    public function resourceUpgradation()
    {
        return $this->belongsTo(ResourceUpgradation::class);
    }

    public function resourceDowngradation()
    {
        return $this->belongsTo(ResourceDowngradation::class);
    }

    public function resourceTransfer()
    {
        return $this->belongsTo(ResourceTransfer::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function vdc()
    {
        return $this->belongsTo(Vdc::class);
    }

    /**
     * Get the user who inserted this task (via resource allocation).
     */
    public function getInsertedByAttribute()
    {
        if ($this->allocation_type === 'upgrade' && $this->resourceUpgradation) {
            return $this->resourceUpgradation->insertedBy;
        } elseif ($this->allocation_type === 'downgrade' && $this->resourceDowngradation) {
            return $this->resourceDowngradation->insertedBy;
        } elseif ($this->allocation_type === 'transfer' && $this->resourceTransfer) {
            return $this->resourceTransfer->insertedBy;
        }

        return null;
    }

    // Get the resource details based on allocation type
    public function getResourceDetailsAttribute()
    {
        if ($this->allocation_type === 'upgrade' && $this->resourceUpgradation) {
            return $this->resourceUpgradation->details;
        } elseif ($this->allocation_type === 'downgrade' && $this->resourceDowngradation) {
            return $this->resourceDowngradation->details;
        } elseif ($this->allocation_type === 'transfer' && $this->resourceTransfer) {
            return $this->resourceTransfer->details;
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

    /**
     * Check if this task is eligible for action (assignment or completion).
     * It must be the oldest task of its kind (unassigned or uncompleted) for its customer.
     */
    public function isEligibleForAction(): bool
    {
        if ($this->completed_at) {
            return true;
        }

        $query = self::where('customer_id', $this->customer_id)
            ->whereNull('completed_at')
            ->where('id', '<', $this->id);

        if ($this->assigned_to) {
            // If assigned, we check if there are older assigned but uncompleted tasks
            $query->whereNotNull('assigned_to');
        } else {
            // If unassigned, we check if there are older unassigned tasks
            $query->whereNull('assigned_to');
        }

        return $query->count() === 0;
    }
}
