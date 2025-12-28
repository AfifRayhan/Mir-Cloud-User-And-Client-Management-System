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
    <h1 style="color: #4f46e5;">New Resource Recommendation</h1>
    <p>A new resource {{ $actionType }} recommendation has been submitted by {{ $sender->name }}.</p>

    <div style="background-color:#f9f9f9; padding:15px; border:1px solid #ddd; border-top: 4px solid #4f46e5;">
        <h2 style="color: #4f46e5;">Customer Information</h2>
        <table width="100%" cellpadding="8" cellspacing="0" style="border-collapse:collapse; margin-top:10px;">
            <tbody>
                <tr>
                    <td style="border:1px solid #ddd; background:#e0e7ff; font-weight:bold; width:30%;">Customer Name</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ $task->customer->customer_name }}</td>
                </tr>
                <tr>
                    <td style="border:1px solid #ddd; background:#e0e7ff; font-weight:bold;">Platform</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ optional($task->customer->platform)->platform_name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td style="border:1px solid #ddd; background:#e0e7ff; font-weight:bold;">Activation Date</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ $task->activation_date->format('M d, Y') }}</td>
                </tr>
                <tr>
                    <td style="border:1px solid #ddd; background:#e0e7ff; font-weight:bold;">Type</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ ucfirst($actionType) }}</td>
                </tr>
                <tr>
                    <td style="border:1px solid #ddd; background:#e0e7ff; font-weight:bold;">Customer Address</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ $task->customer->customer_address }}</td>
                </tr>
                <tr>
                    <td style="border:1px solid #ddd; background:#e0e7ff; font-weight:bold;">PO Number</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ $task->customer->po_number }}</td>
                </tr>
            </tbody>
        </table>

        @if($task->customer->commercial_contact_name || $task->customer->commercial_contact_email || $task->customer->commercial_contact_phone)
        <h2 style="margin-top:20px; color: #4f46e5;">Commercial Contact</h2>
        <table width="100%" cellpadding="8" cellspacing="0" style="border-collapse:collapse; margin-top:10px;">
            <tbody>
                @if($task->customer->commercial_contact_name)
                <tr>
                    <td style="border:1px solid #ddd; background:#e0e7ff; font-weight:bold; width:30%;">Name</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ $task->customer->commercial_contact_name }}</td>
                </tr>
                @endif
                @if($task->customer->commercial_contact_designation)
                <tr>
                    <td style="border:1px solid #ddd; background:#e0e7ff; font-weight:bold;">Designation</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ $task->customer->commercial_contact_designation }}</td>
                </tr>
                @endif
                @if($task->customer->commercial_contact_email)
                <tr>
                    <td style="border:1px solid #ddd; background:#e0e7ff; font-weight:bold;">Email</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ $task->customer->commercial_contact_email }}</td>
                </tr>
                @endif
                @if($task->customer->commercial_contact_phone)
                <tr>
                    <td style="border:1px solid #ddd; background:#e0e7ff; font-weight:bold;">Phone</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ $task->customer->commercial_contact_phone }}</td>
                </tr>
                @endif
            </tbody>
        </table>
        @endif

        @if($task->customer->technical_contact_name || $task->customer->technical_contact_email || $task->customer->technical_contact_phone)
        <h2 style="margin-top:20px; color: #4f46e5;">Technical Contact</h2>
        <table width="100%" cellpadding="8" cellspacing="0" style="border-collapse:collapse; margin-top:10px;">
            <tbody>
                @if($task->customer->technical_contact_name)
                <tr>
                    <td style="border:1px solid #ddd; background:#e0e7ff; font-weight:bold; width:30%;">Name</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ $task->customer->technical_contact_name }}</td>
                </tr>
                @endif
                @if($task->customer->technical_contact_designation)
                <tr>
                    <td style="border:1px solid #ddd; background:#e0e7ff; font-weight:bold;">Designation</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ $task->customer->technical_contact_designation }}</td>
                </tr>
                @endif
                @if($task->customer->technical_contact_email)
                <tr>
                    <td style="border:1px solid #ddd; background:#e0e7ff; font-weight:bold;">Email</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ $task->customer->technical_contact_email }}</td>
                </tr>
                @endif
                @if($task->customer->technical_contact_phone)
                <tr>
                    <td style="border:1px solid #ddd; background:#e0e7ff; font-weight:bold;">Phone</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ $task->customer->technical_contact_phone }}</td>
                </tr>
                @endif
            </tbody>
        </table>
        @endif

        @if($task->customer->optional_contact_name || $task->customer->optional_contact_email || $task->customer->optional_contact_phone)
        <h2 style="margin-top:20px; color: #4f46e5;">Optional Contact</h2>
        <table width="100%" cellpadding="8" cellspacing="0" style="border-collapse:collapse; margin-top:10px;">
            <tbody>
                @if($task->customer->optional_contact_name)
                <tr>
                    <td style="border:1px solid #ddd; background:#e0e7ff; font-weight:bold; width:30%;">Name</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ $task->customer->optional_contact_name }}</td>
                </tr>
                @endif
                @if($task->customer->optional_contact_designation)
                <tr>
                    <td style="border:1px solid #ddd; background:#e0e7ff; font-weight:bold;">Designation</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ $task->customer->optional_contact_designation }}</td>
                </tr>
                @endif
                @if($task->customer->optional_contact_email)
                <tr>
                    <td style="border:1px solid #ddd; background:#e0e7ff; font-weight:bold;">Email</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ $task->customer->optional_contact_email }}</td>
                </tr>
                @endif
                @if($task->customer->optional_contact_phone)
                <tr>
                    <td style="border:1px solid #ddd; background:#e0e7ff; font-weight:bold;">Phone</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ $task->customer->optional_contact_phone }}</td>
                </tr>
                @endif
            </tbody>
        </table>
        @endif
    </div>

    @php
        $isUpgrade = $task->allocation_type === 'upgrade';
        $headerLabel = $isUpgrade ? 'Increase By' : 'Reduce By';
        $headerBg = '#e0e7ff';
        // VDC is not yet assigned during recommendation submission
    @endphp

    <h3 style="color: #4f46e5; margin-top: 25px;">Resource Details</h3>
    @if($task->resourceDetails->count() > 0)
        <table width="100%" cellpadding="8" cellspacing="0" style="border-collapse:collapse; margin-top:10px; font-size: 14px;">
            <thead>
                <tr>
                    <th style="border:1px solid #ddd; background:{{ $headerBg }}; text-align:left; width: 40%;">Service</th>
                    <th style="border:1px solid #ddd; background:{{ $headerBg }}; text-align:left;">Current</th>
                    <th style="border:1px solid #ddd; background:{{ $headerBg }}; text-align:left;">{{ $headerLabel }}</th>
                    <th style="border:1px solid #ddd; background:{{ $headerBg }}; text-align:left;">New</th>
                </tr>
            </thead>
            <tbody>
                @foreach($task->resourceDetails as $detail)
                    @php
                        $amount = $isUpgrade ? $detail->upgrade_amount : $detail->downgrade_amount;
                        $prevValue = $isUpgrade ? ($detail->quantity - $amount) : ($detail->quantity + $amount);
                    @endphp
                    <tr>
                        <td style="border:1px solid #ddd; background:#ffffff;">
                            <strong style="color: #333;">{{ $detail->service->service_name }}</strong>
                            @if($detail->service->unit) <span style="color: #666; font-size: 11px;">({{ $detail->service->unit }})</span> @endif
                        </td>
                        <td style="border:1px solid #ddd; background:#ffffff; color: #666;">{{ $prevValue }} {{ $detail->service->unit }}</td>
                        <td style="border:1px solid #ddd; background:#ffffff; font-weight:bold; color: {{ $isUpgrade ? '#16a34a' : '#ca8a04' }};">
                            {{ $amount }} {{ $detail->service->unit }}
                        </td>
                        <td style="border:1px solid #ddd; background:#f5f7ff; font-weight:bold; color: #4f46e5;">
                            {{ $detail->quantity }} {{ $detail->service->unit }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div style="text-align:center; padding: 20px; background: #f9f9f9; border: 1px solid #ddd;">
            <p style="color: #666; margin: 0;">No resource details available.</p>
        </div>
    @endif
    <div style="margin-top:25px; text-align:center;">

    <a href="{{ route('task-management.index', ['dtid' => $task->id, 'da' => 'view']) }}"
       style="
           display:block;
           width: fit-content;
           margin: 0 auto 12px auto;
           padding:12px 22px;
           background-color:#4f46e5;
           color:#ffffff;
           text-decoration:none;
           font-size:16px;
           font-weight:bold;
           border-radius:4px;
       ">
        🔍 View in Task Management
    </a>

    <a href="{{ route('task-management.index', ['dtid' => $task->id, 'da' => 'assign']) }}"
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
    <p style="margin-top: 30px; font-size: 0.9em; color: #666;">
        This is an automated notification from Mir Cloud Management System.
    </p>
</div>
</body>
</html>