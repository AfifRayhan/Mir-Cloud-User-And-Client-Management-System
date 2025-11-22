<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    protected $primaryKey = 'task_id';

    protected $fillable = [
        'task_name',
        'task_details',
        'assigned_to',
        'task_status',
        'created_by',
    ];

    protected $casts = [
        'task_status' => 'string',
    ];

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

