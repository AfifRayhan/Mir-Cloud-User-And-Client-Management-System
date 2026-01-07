<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResourceTransferDetail extends Model
{
    protected $fillable = [
        'resource_transfer_id',
        'service_id',
        'current_source_quantity',
        'current_target_quantity',
        'transfer_amount',
        'new_source_quantity',
        'new_target_quantity',
    ];

    public function resourceTransfer()
    {
        return $this->belongsTo(ResourceTransfer::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
