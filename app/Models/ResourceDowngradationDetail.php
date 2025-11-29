<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResourceDowngradationDetail extends Model
{
    protected $fillable = [
        'resource_downgradation_id',
        'service_id',
        'quantity',
        'downgrade_amount',
    ];

    public function resourceDowngradation()
    {
        return $this->belongsTo(ResourceDowngradation::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
