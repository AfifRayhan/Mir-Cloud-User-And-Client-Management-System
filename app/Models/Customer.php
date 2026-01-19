<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name',
        'customer_activation_date',
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
        'po_project_sheets',
    ];

    protected $casts = [
        'customer_activation_date' => 'date',
        'processed_at' => 'datetime',
        'po_project_sheets' => 'array',
    ];

    /**
     * Scope a query to only include customers accessible by the given user.
     */
    public function scopeAccessibleBy($query, User $user)
    {
        if ($user->isAdmin() || $user->isProKam() || $user->isProTech() || $user->isManagement() || $user->isBill() || $user->isTech()) {
            return $query;
        }

        if ($user->isKam()) {
            return $query->where('submitted_by', $user->id);
        }

        // Default to no access if role is unrecognized
        return $query->whereRaw('1 = 0');
    }

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

    public function resourceTransfers(): HasMany
    {
        return $this->hasMany(ResourceTransfer::class);
    }

    /**
     * Calculate current resources from upgradation and downgradation history
     * Returns array of service_name => ['test' => quantity, 'billable' => quantity]
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
                    $serviceId = $detail->service_id;
                    $allChanges[] = [
                        'service_id' => $serviceId,
                        'quantity' => $detail->quantity,
                        'activation_date' => $upgradation->activation_date,
                        'inactivation_date' => $upgradation->inactivation_date,
                        'created_at' => $upgradation->created_at,
                        'type' => 'upgrade',
                        'status_id' => $upgradation->status_id,
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
                    $serviceId = $detail->service_id;
                    $allChanges[] = [
                        'service_id' => $serviceId,
                        'quantity' => $detail->quantity,
                        'activation_date' => $downgradation->activation_date,
                        'inactivation_date' => $downgradation->inactivation_date,
                        'created_at' => $downgradation->created_at,
                        'type' => 'downgrade',
                        'status_id' => $downgradation->status_id,
                    ];
                }
            }
        }

        // Get all transfers with their details
        $transfers = $this->resourceTransfers()
            ->with('details.service')
            ->get();

        foreach ($transfers as $transfer) {
            foreach ($transfer->details as $detail) {
                if ($detail->service) {
                    $serviceId = $detail->service_id;
                    // Transfer affects BOTH from and to pools
                    // Add from pool change
                    $allChanges[] = [
                        'service_id' => $serviceId,
                        'quantity' => $detail->new_source_quantity,
                        'activation_date' => $transfer->transfer_datetime,
                        'inactivation_date' => null,
                        'created_at' => $transfer->created_at,
                        'type' => 'transfer_from',
                        'status_id' => $transfer->status_from_id,
                    ];
                    // Add to pool change
                    $allChanges[] = [
                        'service_id' => $serviceId,
                        'quantity' => $detail->new_target_quantity,
                        'activation_date' => $transfer->transfer_datetime,
                        'inactivation_date' => null,
                        'created_at' => $transfer->created_at,
                        'type' => 'transfer_to',
                        'status_id' => $transfer->status_to_id,
                    ];
                }
            }
        }

        // Sort all changes by activation_date DESC, then by created_at DESC
        // This ensures we process the most recent changes first
        usort($allChanges, function ($a, $b) {
            $dateCompare = strcmp($b['activation_date']->format('Y-m-d'), $a['activation_date']->format('Y-m-d'));
            if ($dateCompare !== 0) {
                return $dateCompare;
            }

            return $b['created_at'] <=> $a['created_at'];
        });

        // Process changes in reverse chronological order
        // For each service, find the most recent non-inactivated record for BOTH test and billable pools
        $now = now()->format('Y-m-d');

        foreach ($allChanges as $change) {
            $serviceId = $change['service_id'];

            if (! isset($resources[$serviceId])) {
                $resources[$serviceId] = [
                    'test' => null,
                    'billable' => null,
                ];
            }

            // Determine if this is test or billable based on status
            $testStatusId = \App\Models\CustomerStatus::where('name', 'Test')->first()?->id ?? 1;
            $isTest = $change['status_id'] == $testStatusId;
            $poolKey = $isTest ? 'test' : 'billable';

            // If we already found the latest for this pool, skip
            if ($resources[$serviceId][$poolKey] !== null) {
                continue;
            }

            // Check if this change is not yet inactivated
            $inactivationDate = $change['inactivation_date'];
            if ($inactivationDate === null || $inactivationDate >= $now) {
                $resources[$serviceId][$poolKey] = $change['quantity'];
            } else {
                // If the most recent record is inactivated, the quantity for this pool is 0
                $resources[$serviceId][$poolKey] = 0;
            }
        }

        // Convert any remaining nulls to 0
        foreach ($resources as &$pool) {
            if ($pool['test'] === null) {
                $pool['test'] = 0;
            }
            if ($pool['billable'] === null) {
                $pool['billable'] = 0;
            }
        }

        return $resources;
    }

    public function getResourceQuantity(string $serviceName): int
    {
        $service = \App\Models\Service::where('platform_id', $this->platform_id)
            ->where('service_name', $serviceName)
            ->first();

        if (! $service) {
            return 0;
        }

        $resources = $this->getCurrentResources();

        if (! isset($resources[$service->id])) {
            return 0;
        }

        return ($resources[$service->id]['test'] ?? 0) + ($resources[$service->id]['billable'] ?? 0);
    }

    public function getResourceTestQuantity(string $serviceName): int
    {
        $service = \App\Models\Service::where('platform_id', $this->platform_id)
            ->where('service_name', $serviceName)
            ->first();

        if (! $service) {
            return 0;
        }

        $resources = $this->getCurrentResources();

        return $resources[$service->id]['test'] ?? 0;
    }

    /**
     * Get billable quantity for a specific service
     */
    public function getResourceBillableQuantity(string $serviceName): int
    {
        $service = \App\Models\Service::where('platform_id', $this->platform_id)
            ->where('service_name', $serviceName)
            ->first();

        if (! $service) {
            return 0;
        }

        $resources = $this->getCurrentResources();

        return $resources[$service->id]['billable'] ?? 0;
    }

    /**
     * Check if customer has any resource allocations
     */
    public function hasResourceAllocations(): bool
    {
        return $this->resourceUpgradations()->exists() || $this->resourceDowngradations()->exists();
    }
}
