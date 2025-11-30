<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CloudDetail extends Model
{
    protected $fillable = [
        'customer_id',
        'vcpu',
        'ram',
        'storage',
        'real_ip',
        'vpn',
        'bdix',
        'internet',
        'other_configuration',
        'inserted_by',
    ];

    protected $casts = [
        'other_configuration' => 'array',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
