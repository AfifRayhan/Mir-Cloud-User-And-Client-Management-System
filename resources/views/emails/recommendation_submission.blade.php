<!DOCTYPE html>
<html>
<head>
    <!-- <style>
        body { font-family: Arial, sans-serif; }
        .details-box { background-color: #f9f9f9; padding: 15px; border: 1px solid #ddd; border-radius: 5px; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style> -->
</head>
<body style="font-family: Arial, sans-serif;">
    <h1>New Resource Recommendation</h1>
    <p>A new resource {{ $actionType }} recommendation has been submitted by {{ $sender->name }}.</p>

    <div style="background-color:#f9f9f9; padding:15px; border:1px solid #ddd;">
        <h2>Customer Infromation</h2>
        <p><strong>Customer Name:</strong> {{ $task->customer->customer_name }}</p>
        <p><strong>Platform:</strong> {{ optional($task->customer->platform)->platform_name ?? 'N/A' }}</p>
        <p><strong>Activation Date:</strong> {{ $task->activation_date->format('M d, Y') }}</p>
        <p><strong>Type:</strong> {{ ucfirst($actionType) }}</p>
        <p><strong>Customer Address:</strong> {{ $task->customer->customer_address }}</p>
        <p><strong>PO Number:</strong> {{ $task->customer->po_number }}</p>
        <h2>Commercial Contact</h2>
        <p><strong>Name:</strong> {{ $task->customer->commercial_contact_name }}</p>
        <p><strong>Designation:</strong> {{ $task->customer->commercial_contact_designation }}</p>
        <p><strong>Email:</strong> {{ $task->customer->commercial_contact_email }}</p>
        <p><strong>Phone:</strong> {{ $task->customer->commercial_contact_phone }}</p>
        <h2>Technical Contact</h2>
        <p><strong>Name:</strong> {{ $task->customer->technical_contact_name }}</p>
        <p><strong>Designation:</strong> {{ $task->customer->technical_contact_designation }}</p>
        <p><strong>Email:</strong> {{ $task->customer->technical_contact_email}}</p>
        <p><strong>Phone:</strong> {{ $task->customer->technical_contact_phone }}</p>
        <h2>Optional Contact</h2>
        <p><strong>Name:</strong> {{ $task->customer->optional_contact_name }}</p>
        <p><strong>Designation:</strong> {{ $task->customer->optional_contact_designation }}</p>
        <p><strong>Email:</strong> {{ $task->customer->optional_contact_email }}</p>
        <p><strong>Phone:</strong> {{ $task->customer->optional_contact_phone }}</p>
    </div>

    <h3>Resource Details</h3>
    @if($task->allocation_type === 'upgrade' && $task->resourceUpgradation)
        <table width="100%" cellpadding="6" cellspacing="0" style="border-collapse:collapse; margin-top:10px;">
            <thead>
                <tr>
                    <th style="border:1px solid #ddd; background:#f2f2f2;">Service</th>
                    <th style="border:1px solid #ddd; background:#f2f2f2;">Current Value</th>
                    <th style="border:1px solid #ddd; background:#f2f2f2;">Upgrade Value</th>
                    <th style="border:1px solid #ddd; background:#f2f2f2;">New Value</th>
                </tr>
            </thead>
            <tbody>
                @foreach($task->resourceUpgradation->details as $detail)
                    <tr>
                        <td style="border:1px solid #ddd; background:#f2f2f2;">{{ $detail->service->service_name }} {{ $detail->service->unit ? "({$detail->service->unit})" : '' }}</td>
                        <td style="border:1px solid #ddd; background:#f2f2f2;">{{ max(0, $detail->quantity - $detail->upgrade_amount) }} {{ $detail->service->unit ? "({$detail->service->unit})" : '' }}</td>
                        <td style="border:1px solid #ddd; background:#f2f2f2;">{{ $detail->upgrade_amount }} {{ $detail->service->unit ? "({$detail->service->unit})" : '' }}</td>
                        <td style="border:1px solid #ddd; background:#f2f2f2;">{{ $detail->quantity }} {{ $detail->service->unit ? "({$detail->service->unit})" : '' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @elseif($task->allocation_type === 'downgrade' && $task->resourceDowngradation)
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse; table-layout:fixed; width:100%; margin-top:10px;">
            <thead>
                <tr>
                    <th style="border:1px solid #ddd; padding:8px; text-align:left; background:#f2f2f2; font-weight:700; width:40%;">Service</th>
                    <th style="border:1px solid #ddd; padding:8px; text-align:left; background:#f2f2f2; font-weight:700; width:40%;">Current Value</th>
                    <th style="border:1px solid #ddd; padding:8px; text-align:left; background:#f2f2f2; font-weight:700; width:40%;">Downgrade Value</th>
                    <th style="border:1px solid #ddd; padding:8px; text-align:left; background:#f2f2f2; font-weight:700; width:40%;">New Value</th>
                </tr>
            </thead>
            <tbody>
                @foreach($task->resourceDowngradation->details as $detail)
                    <tr>
                        <td style="border:1px solid #ddd; padding:8px; text-align:left; vertical-align:middle;">{{ $detail->service->service_name }} {{ $detail->service->unit ? "({$detail->service->unit})" : '' }}</td>
                        <td style="border:1px solid #ddd; padding:8px; text-align:left; vertical-align:middle;">{{ max(0, $detail->quantity + $detail->downgrade_amount) }} {{ $detail->service->unit ? "({$detail->service->unit})" : '' }}</td>
                        <td style="border:1px solid #ddd; padding:8px; text-align:left; vertical-align:middle;">{{ $detail->downgrade_amount }} {{ $detail->service->unit ? "({$detail->service->unit})" : '' }}</td>
                        <td style="border:1px solid #ddd; padding:8px; text-align:left; vertical-align:middle;">{{ $detail->quantity }} {{ $detail->service->unit ? "({$detail->service->unit})" : '' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No specific resource details available.</p>
    @endif
    <div style="margin-top:25px; text-align:center;">

    <a href="{{ route('task-management.index') }}"
       style="
           display:block;
           width: fit-content;
           margin: 0 auto 12px auto;
           padding:12px 22px;
           background-color:#2563eb;
           color:#ffffff;
           text-decoration:none;
           font-size:16px;
           font-weight:bold;
           border-radius:4px;
       ">
        🔍 View in Task Management
    </a>

    <a href="{{ route('task-management.index', ['task' => $task->id, 'action' => 'assign']) }}"
       style="
           display:block;
           width: fit-content;
           margin: 0 auto;
           padding:12px 22px;
           background-color:#16a34a;
           color:#ffffff;
           text-decoration:none;
           font-size:16px;
           font-weight:bold;
           border-radius:4px;
       ">
        👤 Assign Task
    </a>

</div>
</body>
</html>
