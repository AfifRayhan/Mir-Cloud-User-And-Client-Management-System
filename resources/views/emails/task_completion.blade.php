<!DOCTYPE html>
<html>
<head>
    <!-- <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style> -->
</head>
<body>
    <h1 style="color: #16a34a;">Task Completion Summary</h1>
    <p>The following task has been completed by {{ $sender->name }}.</p>
 
    @php
        $isUpgrade = $task->allocation_type === 'upgrade';
        
        // Check if this is the first allocation ever for this customer
        $isFirstAllocation = false;
        if ($isUpgrade && $task->resourceUpgradation) {
            $otherUpgradesCount = \App\Models\ResourceUpgradation::where('customer_id', $task->customer_id)
                ->where('id', '<', $task->resource_upgradation_id)
                ->count();
            $otherDowngradesCount = \App\Models\ResourceDowngradation::where('customer_id', $task->customer_id)
                ->count();
            $isFirstAllocation = ($otherUpgradesCount === 0 && $otherDowngradesCount === 0);
        }
        $headerLabel = $isFirstAllocation ? 'Allocation Amount' : 'Upgrade Value';
    @endphp

    <div style="background-color:#f9f9f9; padding:15px; border:1px solid #ddd; border-top: 4px solid #16a34a;">
        <h2 style="color: #16a34a;">Customer Information</h2>
        <table width="100%" cellpadding="8" cellspacing="0" style="border-collapse:collapse; margin-top:10px;">
            <tbody>
                <tr>
                    <td style="border:1px solid #ddd; background:#dcfce7; font-weight:bold; width:30%;">Customer Name</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ $task->customer->customer_name }}</td>
                </tr>
                <tr>
                    <td style="border:1px solid #ddd; background:#dcfce7; font-weight:bold;">Platform</td>
                    @php
                        $platformName = optional($task->customer->platform)->platform_name ?? 'N/A';
                        if ($task->allocation_type === 'downgrade' && $task->resourceDowngradation) {
                             $firstDetail = $task->resourceDowngradation->details->first();
                             if ($firstDetail && $firstDetail->service && $firstDetail->service->platform) {
                                 $platformName = $firstDetail->service->platform->platform_name;
                             }
                        }
                    @endphp
                    <td style="border:1px solid #ddd; padding:8px;">{{ $platformName }}</td>
                </tr>
                @if($task->allocation_type === 'upgrade' && $task->resourceUpgradation)
                <tr>
                    <td style="border:1px solid #ddd; background:#dcfce7; font-weight:bold;">Resource Assignment Date</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ $task->resourceUpgradation->assignment_datetime?->format('M d, Y h:i A') ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td style="border:1px solid #ddd; background:#dcfce7; font-weight:bold;">Resource Allocation Deadline</td>
                    <td style="border:1px solid #ddd; padding:8px; color:#dc2626; font-weight:bold;">{{ $task->resourceUpgradation->deadline_datetime?->format('M d, Y h:i A') ?? 'N/A' }}</td>
                </tr>
                @elseif($task->allocation_type === 'downgrade' && $task->resourceDowngradation)
                <tr>
                    <td style="border:1px solid #ddd; background:#dcfce7; font-weight:bold;">Resource Assignment Date</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ $task->resourceDowngradation->assignment_datetime?->format('M d, Y h:i A') ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td style="border:1px solid #ddd; background:#dcfce7; font-weight:bold;">Resource Allocation Deadline</td>
                    <td style="border:1px solid #ddd; padding:8px; color:#dc2626; font-weight:bold;">{{ $task->resourceDowngradation->deadline_datetime?->format('M d, Y h:i A') ?? 'N/A' }}</td>
                </tr>
                @endif
                <tr>
                    <td style="border:1px solid #ddd; background:#dcfce7; font-weight:bold;">Type</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ $isFirstAllocation ?? false ? 'First Allocation' : ucfirst($actionType) }}</td>
                </tr>
                @if($task->customer->customer_address)
                <tr>
                    <td style="border:1px solid #ddd; background:#dcfce7; font-weight:bold;">Customer Address</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ $task->customer->customer_address }}</td>
                </tr>
                @endif
                @if($task->customer->po_number)
                <tr>
                    <td style="border:1px solid #ddd; background:#dcfce7; font-weight:bold;">PO Number</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ $task->customer->po_number }}</td>
                </tr>
                @endif
            </tbody>
        </table>

        @if($task->customer->commercial_contact_name || $task->customer->commercial_contact_email || $task->customer->commercial_contact_phone || $task->customer->commercial_contact_designation)
        <h2 style="margin-top:20px; color: #16a34a;">Commercial Contact</h2>
        <table width="100%" cellpadding="8" cellspacing="0" style="border-collapse:collapse; margin-top:10px;">
            <tbody>
                @if($task->customer->commercial_contact_name)
                <tr>
                    <td style="border:1px solid #ddd; background:#dcfce7; font-weight:bold; width:30%;">Name</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ $task->customer->commercial_contact_name }}</td>
                </tr>
                @endif
                @if($task->customer->commercial_contact_designation)
                <tr>
                    <td style="border:1px solid #ddd; background:#dcfce7; font-weight:bold;">Designation</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ $task->customer->commercial_contact_designation }}</td>
                </tr>
                @endif
                @if($task->customer->commercial_contact_email)
                <tr>
                    <td style="border:1px solid #ddd; background:#dcfce7; font-weight:bold;">Email</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ $task->customer->commercial_contact_email }}</td>
                </tr>
                @endif
                @if($task->customer->commercial_contact_phone)
                <tr>
                    <td style="border:1px solid #ddd; background:#dcfce7; font-weight:bold;">Phone</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ $task->customer->commercial_contact_phone }}</td>
                </tr>
                @endif
            </tbody>
        </table>
        @endif

        @if($task->customer->technical_contact_name || $task->customer->technical_contact_email || $task->customer->technical_contact_phone || $task->customer->technical_contact_designation)
        <h2 style="margin-top:20px; color: #16a34a;">Technical Contact</h2>
        <table width="100%" cellpadding="8" cellspacing="0" style="border-collapse:collapse; margin-top:10px;">
            <tbody>
                @if($task->customer->technical_contact_name)
                <tr>
                    <td style="border:1px solid #ddd; background:#dcfce7; font-weight:bold; width:30%;">Name</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ $task->customer->technical_contact_name }}</td>
                </tr>
                @endif
                @if($task->customer->technical_contact_designation)
                <tr>
                    <td style="border:1px solid #ddd; background:#dcfce7; font-weight:bold;">Designation</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ $task->customer->technical_contact_designation }}</td>
                </tr>
                @endif
                @if($task->customer->technical_contact_email)
                <tr>
                    <td style="border:1px solid #ddd; background:#dcfce7; font-weight:bold;">Email</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ $task->customer->technical_contact_email }}</td>
                </tr>
                @endif
                @if($task->customer->technical_contact_phone)
                <tr>
                    <td style="border:1px solid #ddd; background:#dcfce7; font-weight:bold;">Phone</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ $task->customer->technical_contact_phone }}</td>
                </tr>
                @endif
            </tbody>
        </table>
        @endif

        @if($task->customer->optional_contact_name || $task->customer->optional_contact_email || $task->customer->optional_contact_phone || $task->customer->optional_contact_designation)
        <h2 style="margin-top:20px; color: #16a34a;">Optional Contact</h2>
        <table width="100%" cellpadding="8" cellspacing="0" style="border-collapse:collapse; margin-top:10px;">
            <tbody>
                @if($task->customer->optional_contact_name)
                <tr>
                    <td style="border:1px solid #ddd; background:#dcfce7; font-weight:bold; width:30%;">Name</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ $task->customer->optional_contact_name }}</td>
                </tr>
                @endif
                @if($task->customer->optional_contact_designation)
                <tr>
                    <td style="border:1px solid #ddd; background:#dcfce7; font-weight:bold;">Designation</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ $task->customer->optional_contact_designation }}</td>
                </tr>
                @endif
                @if($task->customer->optional_contact_email)
                <tr>
                    <td style="border:1px solid #ddd; background:#dcfce7; font-weight:bold;">Email</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ $task->customer->optional_contact_email }}</td>
                </tr>
                @endif
                @if($task->customer->optional_contact_phone)
                <tr>
                    <td style="border:1px solid #ddd; background:#dcfce7; font-weight:bold;">Phone</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ $task->customer->optional_contact_phone }}</td>
                </tr>
                @endif
            </tbody>
        </table>
        @endif
    </div>

    @php
        $headerLabel = $isFirstAllocation ? 'Allocation Amount' : 'Upgrade Value';
    @endphp

    <h3 style="color: #16a34a;">Resource Details (Task ID: {{ $task->task_id ?? 'N/A' }})</h3>
    @if($task->allocation_type === 'upgrade' && $task->resourceUpgradation)
        <table width="100%" cellpadding="6" cellspacing="0" style="border-collapse:collapse; margin-top:10px;">
            <thead>
                <tr>
                    <th style="border:1px solid #ddd; background:#dcfce7;">VDC Name</th>
                    <th style="border:1px solid #ddd; background:#dcfce7;">Service</th>
                    @if(!$isFirstAllocation)
                        <th style="border:1px solid #ddd; background:#dcfce7;">Current Value</th>
                    @endif
                    <th style="border:1px solid #ddd; background:#dcfce7;">{{ $headerLabel }}</th>
                    @if(!$isFirstAllocation)
                        <th style="border:1px solid #ddd; background:#dcfce7;">New Value</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($task->resourceUpgradation->details as $detail)
                    <tr>
                        <td style="border:1px solid #ddd; background:#f9f9f9;">{{ optional($task->vdc)->vdc_name ?? 'N/A' }}</td>
                        <td style="border:1px solid #ddd; background:#ffffff;">
                            <strong style="color: #333;">{{ $detail->service->service_name }}</strong>
                            @if($detail->service->unit) <span style="color: #666; font-size: 11px;">({{ $detail->service->unit }})</span> @endif
                        </td>
                        @if(!$isFirstAllocation)
                            <td style="border:1px solid #ddd; background:#ffffff; color: #666;">{{ max(0, $detail->quantity - $detail->upgrade_amount) }} {{ $detail->service->unit }}</td>
                        @endif
                        <td style="border:1px solid #ddd; background:#ffffff; font-weight:bold; color: #16a34a;">{{ $detail->upgrade_amount }} {{ $detail->service->unit }}</td>
                        @if(!$isFirstAllocation)
                            <td style="border:1px solid #ddd; background:#f5f7ff; font-weight:bold; color: #4f46e5;">{{ $detail->quantity }} {{ $detail->service->unit }}</td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    @elseif($task->allocation_type === 'downgrade' && $task->resourceDowngradation)
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse; table-layout:fixed; width:100%; margin-top:10px;">
            <thead>
                <tr>
                    <th style="border:1px solid #ddd; padding:8px; text-align:left; background:#dcfce7; font-weight:700; width:20%;">VDC Name</th>
                    <th style="border:1px solid #ddd; padding:8px; text-align:left; background:#dcfce7; font-weight:700; width:25%;">Service</th>
                    <th style="border:1px solid #ddd; padding:8px; text-align:left; background:#dcfce7; font-weight:700; width:20%;">Current Value</th>
                    <th style="border:1px solid #ddd; padding:8px; text-align:left; background:#dcfce7; font-weight:700; width:15%;">Downgrade Value</th>
                    <th style="border:1px solid #ddd; padding:8px; text-align:left; background:#dcfce7; font-weight:700; width:20%;">New Value</th>
                </tr>
            </thead>
            <tbody>
                @foreach($task->resourceDowngradation->details as $detail)
                    <tr>
                        <td style="border:1px solid #ddd; padding:8px; text-align:left; vertical-align:middle;">{{ optional($task->vdc)->vdc_name ?? 'N/A' }}</td>
                        <td style="border:1px solid #ddd; padding:8px; text-align:left; vertical-align:middle;">
                            <strong style="color: #333;">{{ $detail->service->service_name }}</strong>
                            @if($detail->service->unit) <span style="color: #666; font-size: 11px;">({{ $detail->service->unit }})</span> @endif
                        </td>
                        <td style="border:1px solid #ddd; padding:8px; text-align:left; vertical-align:middle; color: #666;">{{ max(0, $detail->quantity + $detail->downgrade_amount) }} {{ $detail->service->unit }}</td>
                        <td style="border:1px solid #ddd; padding:8px; text-align:left; vertical-align:middle; font-weight:bold; color: #ca8a04;">{{ $detail->downgrade_amount }} {{ $detail->service->unit }}</td>
                        <td style="border:1px solid #ddd; padding:8px; text-align:left; vertical-align:middle; background:#f5f7ff; font-weight:bold; color: #4f46e5;">{{ $detail->quantity }} {{ $detail->service->unit }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No specific resource details available.</p>
    @endif
    <p style="margin-top: 30px; font-size: 0.9em; color: #666;">
        This is an automated notification from Mir Cloud Management System.
    </p>
</body>
</html>
