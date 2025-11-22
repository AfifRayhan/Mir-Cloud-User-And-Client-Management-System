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
        'billing_type',
    ];

    protected $casts = [
        'real_ip' => 'boolean',
        'vpn' => 'boolean',
        'bdix' => 'boolean',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
