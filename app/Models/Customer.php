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
        $resources = [];
        
        // Collect all resource changes (both upgrades and downgrades) with their timestamps
        $allChanges = [];
        
        // Get all upgradations with their details
        $upgradations = $this->resourceUpgradations()
            ->with('details.service')
            ->get();
        
        foreach ($upgradations as $upgradation) {
            foreach ($upgradation->details as $detail) {
                if ($detail->service) {
                    $serviceName = $detail->service->service_name;
                    $allChanges[] = [
                        'service_name' => $serviceName,
                        'quantity' => $detail->quantity,
                        'activation_date' => $upgradation->activation_date,
                        'inactivation_date' => $upgradation->inactivation_date,
                        'created_at' => $upgradation->created_at,
                        'type' => 'upgrade'
                    ];
                }
            }
        }
        
        // Get all downgradations with their details
        $downgradations = $this->resourceDowngradations()
            ->with('details.service')
            ->get();
        
        foreach ($downgradations as $downgradation) {
            foreach ($downgradation->details as $detail) {
                if ($detail->service) {
                    $serviceName = $detail->service->service_name;
                    $allChanges[] = [
                        'service_name' => $serviceName,
                        'quantity' => $detail->quantity,
                        'activation_date' => $downgradation->activation_date,
                        'inactivation_date' => $downgradation->inactivation_date,
                        'created_at' => $downgradation->created_at,
                        'type' => 'downgrade'
                    ];
                }
            }
        }
        
        // Sort all changes by activation_date DESC, then by created_at DESC
        // This ensures we process the most recent changes first
        usort($allChanges, function($a, $b) {
            $dateCompare = strcmp($b['activation_date'], $a['activation_date']);
            if ($dateCompare !== 0) {
                return $dateCompare;
            }
            return strcmp($b['created_at'], $a['created_at']);
        });
        
        // Process changes in reverse chronological order
        // For each service, use the quantity from the most recent non-inactivated record
        $now = now()->format('Y-m-d');
        
        foreach ($allChanges as $change) {
            $serviceName = $change['service_name'];
            
            // Skip if we already found a record for this service
            if (isset($resources[$serviceName])) {
                continue;
            }
            
            // Check if this change is not yet inactivated
            // We show resources even if activation date is in the future
            if ($change['inactivation_date'] >= $now) {
                // This is the most recent non-inactivated change for this service
                $resources[$serviceName] = $change['quantity'];
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
