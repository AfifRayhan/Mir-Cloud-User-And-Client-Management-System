<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Customer extends Model
{
    protected $fillable = [
        'customer_name',
        'activation_date',
        'customer_address',
        'bin_number',
        'po_number',
        'commercial_contact_name',
        'commercial_contact_designation',
        'commercial_contact_email',
        'commercial_contact_phone',
        'technical_contact_name',
        'technical_contact_designation',
        'technical_contact_email',
        'technical_contact_phone',
        'optional_contact_name',
        'optional_contact_designation',
        'optional_contact_email',
        'optional_contact_phone',
        'platform_id',
        'submitted_by',
        'processed_by',
        'processed_at',
    ];

    protected $casts = [
        'activation_date' => 'date',
        'processed_at' => 'datetime',
    ];

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function cloudDetail(): HasOne
    {
        return $this->hasOne(CloudDetail::class);
    }

    public function platform(): BelongsTo
    {
        return $this->belongsTo(Platform::class);
    }
}
