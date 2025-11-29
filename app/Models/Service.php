<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_name',
        'unit',
        'unit_price',
        'inserted_by',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
    ];

    public function insertedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inserted_by');
    }
}

