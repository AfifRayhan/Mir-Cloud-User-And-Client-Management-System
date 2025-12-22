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
    <h1 style="color: #ea580c;">New Task Assignment</h1>
    <p>You have been assigned a new task by {{ $sender->name }}.</p>
    
    <div style="background-color:#f9f9f9; padding:15px; border:1px solid #ddd; border-top: 4px solid #ea580c;">
        <h2 style="color: #4f46e5;">Customer Information</h2>
        <table width="100%" cellpadding="8" cellspacing="0" style="border-collapse:collapse; margin-top:10px;">
            <tbody>
                <tr>
                    <td style="border:1px solid #ddd; background:#ffedd5; font-weight:bold; width:30%;">Customer Name</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ $task->customer->customer_name }}</td>
                </tr>
                <tr>
                    <td style="border:1px solid #ddd; background:#ffedd5; font-weight:bold;">Platform</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ optional($task->customer->platform)->platform_name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td style="border:1px solid #ddd; background:#ffedd5; font-weight:bold;">Activation Date</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ $task->activation_date->format('M d, Y') }}</td>
                </tr>
                <tr>
                    <td style="border:1px solid #ddd; background:#ffedd5; font-weight:bold;">Type</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ ucfirst($actionType) }}</td>
                </tr>
                <tr>
                    <td style="border:1px solid #ddd; background:#ffedd5; font-weight:bold;">Customer Address</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ $task->customer->customer_address }}</td>
                </tr>
                <tr>
                    <td style="border:1px solid #ddd; background:#ffedd5; font-weight:bold;">PO Number</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ $task->customer->po_number }}</td>
                </tr>
            </tbody>
        </table>

        @if($task->customer->commercial_contact_name || $task->customer->commercial_contact_email || $task->customer->commercial_contact_phone)
        <h2 style="margin-top:20px; color: #ea580c;">Commercial Contact</h2>
        <table width="100%" cellpadding="8" cellspacing="0" style="border-collapse:collapse; margin-top:10px;">
            <tbody>
                @if($task->customer->commercial_contact_name)
                <tr>
                    <td style="border:1px solid #ddd; background:#ffedd5; font-weight:bold; width:30%;">Name</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ $task->customer->commercial_contact_name }}</td>
                </tr>
                @endif
                @if($task->customer->commercial_contact_designation)
                <tr>
                    <td style="border:1px solid #ddd; background:#ffedd5; font-weight:bold;">Designation</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ $task->customer->commercial_contact_designation }}</td>
                </tr>
                @endif
                @if($task->customer->commercial_contact_email)
                <tr>
                    <td style="border:1px solid #ddd; background:#ffedd5; font-weight:bold;">Email</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ $task->customer->commercial_contact_email }}</td>
                </tr>
                @endif
                @if($task->customer->commercial_contact_phone)
                <tr>
                    <td style="border:1px solid #ddd; background:#ffedd5; font-weight:bold;">Phone</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ $task->customer->commercial_contact_phone }}</td>
                </tr>
                @endif
            </tbody>
        </table>
        @endif

        @if($task->customer->technical_contact_name || $task->customer->technical_contact_email || $task->customer->technical_contact_phone)
        <h2 style="margin-top:20px; color: #ea580c;">Technical Contact</h2>
        <table width="100%" cellpadding="8" cellspacing="0" style="border-collapse:collapse; margin-top:10px;">
            <tbody>
                @if($task->customer->technical_contact_name)
                <tr>
                    <td style="border:1px solid #ddd; background:#ffedd5; font-weight:bold; width:30%;">Name</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ $task->customer->technical_contact_name }}</td>
                </tr>
                @endif
                @if($task->customer->technical_contact_designation)
                <tr>
                    <td style="border:1px solid #ddd; background:#ffedd5; font-weight:bold;">Designation</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ $task->customer->technical_contact_designation }}</td>
                </tr>
                @endif
                @if($task->customer->technical_contact_email)
                <tr>
                    <td style="border:1px solid #ddd; background:#ffedd5; font-weight:bold;">Email</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ $task->customer->technical_contact_email }}</td>
                </tr>
                @endif
                @if($task->customer->technical_contact_phone)
                <tr>
                    <td style="border:1px solid #ddd; background:#ffedd5; font-weight:bold;">Phone</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ $task->customer->technical_contact_phone }}</td>
                </tr>
                @endif
            </tbody>
        </table>
        @endif

        @if($task->customer->optional_contact_name || $task->customer->optional_contact_email || $task->customer->optional_contact_phone)
        <h2 style="margin-top:20px; color: #ea580c;">Optional Contact</h2>
        <table width="100%" cellpadding="8" cellspacing="0" style="border-collapse:collapse; margin-top:10px;">
            <tbody>
                @if($task->customer->optional_contact_name)
                <tr>
                    <td style="border:1px solid #ddd; background:#ffedd5; font-weight:bold; width:30%;">Name</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ $task->customer->optional_contact_name }}</td>
                </tr>
                @endif
                @if($task->customer->optional_contact_designation)
                <tr>
                    <td style="border:1px solid #ddd; background:#ffedd5; font-weight:bold;">Designation</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ $task->customer->optional_contact_designation }}</td>
                </tr>
                @endif
                @if($task->customer->optional_contact_email)
                <tr>
                    <td style="border:1px solid #ddd; background:#ffedd5; font-weight:bold;">Email</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ $task->customer->optional_contact_email }}</td>
                </tr>
                @endif
                @if($task->customer->optional_contact_phone)
                <tr>
                    <td style="border:1px solid #ddd; background:#ffedd5; font-weight:bold;">Phone</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ $task->customer->optional_contact_phone }}</td>
                </tr>
                @endif
            </tbody>
        </table>
        @endif
    </div>

    <h3 style="color: #ea580c;">Resource Details</h3>
    @if($task->allocation_type === 'upgrade' && $task->resourceUpgradation)
        <table width="100%" cellpadding="6" cellspacing="0" style="border-collapse:collapse; margin-top:10px;">
            <thead>
                <tr>
                    <th style="border:1px solid #ddd; background:#ffedd5;">Service</th>
                    <th style="border:1px solid #ddd; background:#ffedd5;">Current Value</th>
                    <th style="border:1px solid #ddd; background:#ffedd5;">Upgrade Value</th>
                    <th style="border:1px solid #ddd; background:#ffedd5;">New Value</th>
                </tr>
            </thead>
            <tbody>
                @foreach($task->resourceUpgradation->details as $detail)
                    <tr>
                        <td style="border:1px solid #ddd; background:#f9f9f9;">{{ $detail->service->service_name }} {{ $detail->service->unit ? "({$detail->service->unit})" : '' }}</td>
                        <td style="border:1px solid #ddd; background:#f9f9f9;">{{ max(0, $detail->quantity - $detail->upgrade_amount) }} {{ $detail->service->unit ? "({$detail->service->unit})" : '' }}</td>
                        <td style="border:1px solid #ddd; background:#f9f9f9;">{{ $detail->upgrade_amount }} {{ $detail->service->unit ? "({$detail->service->unit})" : '' }}</td>
                        <td style="border:1px solid #ddd; background:#f9f9f9;">{{ $detail->quantity }} {{ $detail->service->unit ? "({$detail->service->unit})" : '' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @elseif($task->allocation_type === 'downgrade' && $task->resourceDowngradation)
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse; table-layout:fixed; width:100%; margin-top:10px;">
            <thead>
                <tr>
                    <th style="border:1px solid #ddd; padding:8px; text-align:left; background:#ffedd5; font-weight:700; width:40%;">Service</th>
                    <th style="border:1px solid #ddd; padding:8px; text-align:left; background:#ffedd5; font-weight:700; width:40%;">Current Value</th>
                    <th style="border:1px solid #ddd; padding:8px; text-align:left; background:#ffedd5; font-weight:700; width:40%;">Downgrade Value</th>
                    <th style="border:1px solid #ddd; padding:8px; text-align:left; background:#ffedd5; font-weight:700; width:40%;">New Value</th>
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
    <a href="{{ route('my-tasks.index', ['dtid' => $task->id, 'da' => 'view']) }}"
        style="
           display:block;
           width: fit-content;
           margin: 0 auto 12px auto;
           padding:12px 22px;
           background-color:#ea580c;
           color:#ffffff;
           text-decoration:none;
           font-size:16px;
           font-weight:bold;
           border-radius:4px;
       ">
        🔍 View in My Task
    </a>
    <a href="{{ route('my-tasks.index', ['dtid' => $task->id, 'da' => 'complete']) }}"
        style="
           display:block;
           width: fit-content;
           margin: 12px auto 0 auto;
           padding:12px 22px;
           background-color:#16a34a;
           color:#ffffff;
           text-decoration:none;
           font-size:16px;
           font-weight:bold;
           border-radius:4px;
       ">
        ✅ Complete Task
    </a>
    
</div>
</body>
</html>
