<!DOCTYPE html>
<html>
<head>
    <style>
        .button {
            display: inline-block;
            padding: 10px 20px;
            margin: 10px 5px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            color: white !important;
        }
        .btn-approve { background-color: #16a34a; }
        .btn-undo { background-color: #dc2626; }
    </style>
</head>
<body>
    <h1 style="color: #8b5cf6;">Resource Allocation Confirmed</h1>
    <p>The following task has been completed by <strong>{{ $sender->name }}</strong>.</p>

    <div style="background-color:#f9f9f9; padding:15px; border:1px solid #ddd; border-top: 4px solid #8b5cf6;">
        <h2 style="color: #8b5cf6;">Customer Information</h2>
        <table width="100%" cellpadding="8" cellspacing="0" style="border-collapse:collapse; margin-top:10px;">
            <tbody>
                <tr>
                    <td style="border:1px solid #ddd; background:#f3f4f6; font-weight:bold; width:30%;">Customer Name</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ $task->customer->customer_name }}</td>
                </tr>
                <tr>
                    <td style="border:1px solid #ddd; background:#f3f4f6; font-weight:bold;">Platform</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ optional($task->customer->platform)->platform_name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td style="border:1px solid #ddd; background:#f3f4f6; font-weight:bold;">Activation Date</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ $task->activation_date->format('M d, Y') }}</td>
                </tr>
                <tr>
                    <td style="border:1px solid #ddd; background:#f3f4f6; font-weight:bold;">Type</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ ucfirst($actionType) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <h3 style="color: #8b5cf6;">Resource Details</h3>
    @if($task->allocation_type === 'upgrade' && $task->resourceUpgradation)
        <table width="100%" cellpadding="6" cellspacing="0" style="border-collapse:collapse; margin-top:10px;">
            <thead>
                <tr>
                    <th style="border:1px solid #ddd; background:#f3f4f6;">VDC Name</th>
                    <th style="border:1px solid #ddd; background:#f3f4f6;">Service</th>
                    <th style="border:1px solid #ddd; background:#f3f4f6;">Current Value</th>
                    <th style="border:1px solid #ddd; background:#f3f4f6;">Upgrade Value</th>
                    <th style="border:1px solid #ddd; background:#f3f4f6;">New Value</th>
                </tr>
            </thead>
            <tbody>
                @foreach($task->resourceUpgradation->details as $detail)
                    <tr>
                        <td style="border:1px solid #ddd; background:#fff;">{{ optional($task->vdc)->vdc_name ?? 'N/A' }}</td>
                        <td style="border:1px solid #ddd; background:#fff;">{{ $detail->service->service_name }} {{ $detail->service->unit ? "({$detail->service->unit})" : '' }}</td>
                        <td style="border:1px solid #ddd; background:#fff;">{{ max(0, $detail->quantity - $detail->upgrade_amount) }} {{ $detail->service->unit ? "({$detail->service->unit})" : '' }}</td>
                        <td style="border:1px solid #ddd; background:#fff;">{{ $detail->upgrade_amount }} {{ $detail->service->unit ? "({$detail->service->unit})" : '' }}</td>
                        <td style="border:1px solid #ddd; background:#fff;">{{ $detail->quantity }} {{ $detail->service->unit ? "({$detail->service->unit})" : '' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @elseif($task->allocation_type === 'downgrade' && $task->resourceDowngradation)
        <table width="100%" cellpadding="6" cellspacing="0" style="border-collapse:collapse; margin-top:10px;">
            <thead>
                <tr>
                    <th style="border:1px solid #ddd; background:#f3f4f6;">VDC Name</th>
                    <th style="border:1px solid #ddd; background:#f3f4f6;">Service</th>
                    <th style="border:1px solid #ddd; background:#f3f4f6;">Current Value</th>
                    <th style="border:1px solid #ddd; background:#f3f4f6;">Downgrade Value</th>
                    <th style="border:1px solid #ddd; background:#f3f4f6;">New Value</th>
                </tr>
            </thead>
            <tbody>
                @foreach($task->resourceDowngradation->details as $detail)
                    <tr>
                        <td style="border:1px solid #ddd; background:#fff;">{{ optional($task->vdc)->vdc_name ?? 'N/A' }}</td>
                        <td style="border:1px solid #ddd; background:#fff;">{{ $detail->service->service_name }} {{ $detail->service->unit ? "({$detail->service->unit})" : '' }}</td>
                        <td style="border:1px solid #ddd; background:#fff;">{{ max(0, $detail->quantity + $detail->downgrade_amount) }} {{ $detail->service->unit ? "({$detail->service->unit})" : '' }}</td>
                        <td style="border:1px solid #ddd; background:#fff;">{{ $detail->downgrade_amount }} {{ $detail->service->unit ? "({$detail->service->unit})" : '' }}</td>
                        <td style="border:1px solid #ddd; background:#fff;">{{ $detail->quantity }} {{ $detail->service->unit ? "({$detail->service->unit})" : '' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No specific resource details available.</p>
    @endif

    <div style="margin-top: 30px; text-align: center;">
        <a href="{{ URL::signedRoute('tasks.approve', ['task' => $task->id]) }}" style="
           display:block;
           width: fit-content;
           margin: 0 auto 12px auto;
           padding:12px 22px;
           background-color:#16a34a;
           color:#ffffff;
           text-decoration:none;
           font-size:16px;
           font-weight:bold;
           border-radius:4px;">
           ✅ Approve Task
        </a>
        <a href="{{ URL::signedRoute('tasks.undo', ['task' => $task->id]) }}" style="
           display:block;
           width: fit-content;
           margin: 12px auto 0 auto;
           padding:12px 22px;
           background-color:#e01b1b;
           color:#ffffff;
           text-decoration:none;
           font-size:16px;
           font-weight:bold;
           border-radius:4px;">
           ❌ Undo Task
        </a>
    </div>

    <p style="margin-top: 30px; font-size: 0.9em; color: #666;">
        This is an automated notification from Mir Cloud Management System.
    </p>
</body>
</html>
