<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResourceUpgradationDetail extends Model
{
    protected $fillable = [
        'resource_upgradation_id',
        'service_id',
        'quantity',
        'upgrade_amount',
    ];

    public function resourceUpgradation()
    {
        return $this->belongsTo(ResourceUpgradation::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
