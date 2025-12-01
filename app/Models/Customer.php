<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function platform(): BelongsTo
    {
        return $this->belongsTo(Platform::class);
    }

    public function resourceUpgradations(): HasMany
    {
        return $this->hasMany(ResourceUpgradation::class);
    }

    public function resourceDowngradations(): HasMany
    {
        return $this->hasMany(ResourceDowngradation::class);
    }

    /**
     * Calculate current resources from upgradation and downgradation history
     * Returns array of service_name => quantity
     */
    public function getCurrentResources(): array
    {
        // Get the most recent active upgradation/downgradation for each service
        $resources = [];
        
        // Get all active upgradations
        $upgradations = $this->resourceUpgradations()
            ->where('activation_date', '<=', now())
            ->where('inactivation_date', '>=', now())
            ->with('details.service')
            ->orderBy('activation_date', 'desc')
            ->get();
        
        // Get all active downgradations
        $downgradations = $this->resourceDowngradations()
            ->where('activation_date', '<=', now())
            ->where('inactivation_date', '>=', now())
            ->with('details.service')
            ->orderBy('activation_date', 'desc')
            ->get();
        
        // Process upgradations - use the quantity from the most recent record
        foreach ($upgradations as $upgradation) {
            foreach ($upgradation->details as $detail) {
                $serviceName = $detail->service->service_name;
                if (!isset($resources[$serviceName])) {
                    $resources[$serviceName] = $detail->quantity;
                }
            }
        }
        
        // Process downgradations - use the quantity from the most recent record
        foreach ($downgradations as $downgradation) {
            foreach ($downgradation->details as $detail) {
                $serviceName = $detail->service->service_name;
                // Downgradation records also store the final quantity, not just the reduction
                $resources[$serviceName] = $detail->quantity;
            }
        }
        
        return $resources;
    }

    /**
     * Get quantity for a specific service
     */
    public function getResourceQuantity(string $serviceName): int
    {
        $resources = $this->getCurrentResources();
        return $resources[$serviceName] ?? 0;
    }

    /**
     * Check if customer has any resource allocations
     */
    public function hasResourceAllocations(): bool
    {
        return $this->resourceUpgradations()->exists() || $this->resourceDowngradations()->exists();
    }
}
