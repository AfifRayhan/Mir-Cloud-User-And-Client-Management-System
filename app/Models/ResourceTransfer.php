<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResourceTransfer extends Model
{
    protected $fillable = [
        'customer_id',
        'status_from_id',
        'status_to_id',
        'transfer_datetime',
        'inserted_by',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function statusFrom()
    {
        return $this->belongsTo(CustomerStatus::class, 'status_from_id');
    }

    public function statusTo()
    {
        return $this->belongsTo(CustomerStatus::class, 'status_to_id');
    }

    public function insertedBy()
    {
        return $this->belongsTo(User::class, 'inserted_by');
    }

    public function details()
    {
        return $this->hasMany(ResourceTransferDetail::class);
    }

    protected function casts(): array
    {
        return [
            'transfer_datetime' => 'datetime',
        ];
    }
}
