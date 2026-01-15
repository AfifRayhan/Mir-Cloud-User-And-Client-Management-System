<!DOCTYPE html>
<html>
<head>
</head>
<body style="font-family: Arial, sans-serif;">
    <h1 style="color: #0d6efd;">New Transfer Assignment</h1>
    <p>A new resource transfer has been confirmed by {{ $sender->name }}.</p>
    
    @php
        // Determine if this is Test to Billable (status_id 3) or Billable to Test (status_id 4)
        $isTestToBillable = ($task->status_id ?? $transfer->status_from_id) == 2 || 
                           ($task->status_id ?? $transfer->status_to_id) == 1;
        $firstPoolLabel = $isTestToBillable ? 'Billable' : 'Test';
        $secondPoolLabel = $isTestToBillable ? 'Test' : 'Billable';
    @endphp
    
    <div style="background-color:#f9f9f9; padding:15px; border:1px solid #ddd; border-top: 4px solid #0d6efd;">
        <h2 style="color: #4f46e5;">Customer Information</h2>
        <table width="100%" cellpadding="8" cellspacing="0" style="border-collapse:collapse; margin-top:10px;">
            <tbody>
                <tr>
                    <td style="border:1px solid #ddd; background:#dbeafe; font-weight:bold; width:30%;">Customer Name</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ $customer->customer_name }}</td>
                </tr>
                <tr>
                    <td style="border:1px solid #ddd; background:#dbeafe; font-weight:bold;">Platform</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ optional($customer->platform)->platform_name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td style="border:1px solid #ddd; background:#dbeafe; font-weight:bold;">Transfer Assignment Date</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ $transfer->transfer_datetime?->format('M d, Y h:i A') ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td style="border:1px solid #ddd; background:#dbeafe; font-weight:bold;">Type</td>
                    <td style="border:1px solid #ddd; padding:8px;">Transfer</td>
                </tr>
                @if($customer->customer_address)
                <tr>
                    <td style="border:1px solid #ddd; background:#dbeafe; font-weight:bold;">Customer Address</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ $customer->customer_address }}</td>
                </tr>
                @endif
                @if($customer->po_number)
                <tr>
                    <td style="border:1px solid #ddd; background:#dbeafe; font-weight:bold;">PO Number</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ $customer->po_number }}</td>
                </tr>
                @endif
            </tbody>
        </table>

        @if($customer->commercial_contact_name || $customer->commercial_contact_email || $customer->commercial_contact_phone || $customer->commercial_contact_designation)
        <h2 style="margin-top:20px; color: #0d6efd;">Commercial Contact</h2>
        <table width="100%" cellpadding="8" cellspacing="0" style="border-collapse:collapse; margin-top:10px;">
            <tbody>
                @if($customer->commercial_contact_name)
                <tr>
                    <td style="border:1px solid #ddd; background:#dbeafe; font-weight:bold; width:30%;">Name</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ $customer->commercial_contact_name }}</td>
                </tr>
                @endif
                @if($customer->commercial_contact_designation)
                <tr>
                    <td style="border:1px solid #ddd; background:#dbeafe; font-weight:bold;">Designation</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ $customer->commercial_contact_designation }}</td>
                </tr>
                @endif
                @if($customer->commercial_contact_email)
                <tr>
                    <td style="border:1px solid #ddd; background:#dbeafe; font-weight:bold;">Email</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ $customer->commercial_contact_email }}</td>
                </tr>
                @endif
                @if($customer->commercial_contact_phone)
                <tr>
                    <td style="border:1px solid #ddd; background:#dbeafe; font-weight:bold;">Phone</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ $customer->commercial_contact_phone }}</td>
                </tr>
                @endif
            </tbody>
        </table>
        @endif

        @if($customer->technical_contact_name || $customer->technical_contact_email || $customer->technical_contact_phone || $customer->technical_contact_designation)
        <h2 style="margin-top:20px; color: #0d6efd;">Technical Contact</h2>
        <table width="100%" cellpadding="8" cellspacing="0" style="border-collapse:collapse; margin-top:10px;">
            <tbody>
                @if($customer->technical_contact_name)
                <tr>
                    <td style="border:1px solid #ddd; background:#dbeafe; font-weight:bold; width:30%;">Name</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ $customer->technical_contact_name }}</td>
                </tr>
                @endif
                @if($customer->technical_contact_designation)
                <tr>
                    <td style="border:1px solid #ddd; background:#dbeafe; font-weight:bold;">Designation</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ $customer->technical_contact_designation }}</td>
                </tr>
                @endif
                @if($customer->technical_contact_email)
                <tr>
                    <td style="border:1px solid #ddd; background:#dbeafe; font-weight:bold;">Email</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ $customer->technical_contact_email }}</td>
                </tr>
                @endif
                @if($customer->technical_contact_phone)
                <tr>
                    <td style="border:1px solid #ddd; background:#dbeafe; font-weight:bold;">Phone</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ $customer->technical_contact_phone }}</td>
                </tr>
                @endif
            </tbody>
        </table>
        @endif

        @if($customer->optional_contact_name || $customer->optional_contact_email || $customer->optional_contact_phone || $customer->optional_contact_designation)
        <h2 style="margin-top:20px; color: #0d6efd;">Optional Contact</h2>
        <table width="100%" cellpadding="8" cellspacing="0" style="border-collapse:collapse; margin-top:10px;">
            <tbody>
                @if($customer->optional_contact_name)
                <tr>
                    <td style="border:1px solid #ddd; background:#dbeafe; font-weight:bold; width:30%;">Name</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ $customer->optional_contact_name }}</td>
                </tr>
                @endif
                @if($customer->optional_contact_designation)
                <tr>
                    <td style="border:1px solid #ddd; background:#dbeafe; font-weight:bold;">Designation</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ $customer->optional_contact_designation }}</td>
                </tr>
                @endif
                @if($customer->optional_contact_email)
                <tr>
                    <td style="border:1px solid #ddd; background:#dbeafe; font-weight:bold;">Email</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ $customer->optional_contact_email }}</td>
                </tr>
                @endif
                @if($customer->optional_contact_phone)
                <tr>
                    <td style="border:1px solid #ddd; background:#dbeafe; font-weight:bold;">Phone</td>
                    <td style="border:1px solid #ddd; padding:8px;">{{ $customer->optional_contact_phone }}</td>
                </tr>
                @endif
            </tbody>
        </table>
        @endif
    </div>

    <h3 style="color: #0d6efd;">Resource Details @if($task)(Task ID: {{ $task->task_id ?? 'N/A' }})@endif</h3>
    <table width="100%" cellpadding="6" cellspacing="0" style="border-collapse:collapse; margin-top:10px;">
        <thead>
            <tr>
                <th style="border:1px solid #ddd; background:#dbeafe;">Service</th>
                <th style="border:1px solid #ddd; background:#dbeafe;">Billable</th>
                <th style="border:1px solid #ddd; background:#dbeafe;">Transfer Amount</th>
                <th style="border:1px solid #ddd; background:#dbeafe;">Test</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transfer->details as $detail)
                @php
                    // Standardized Columns: Billable (Left) | Transfer | Test (Right)
                    // We always show the FINAL New Total for each pool.

                    if ($isTestToBillable) {
                        // Test -> Billable
                        // Billable (Target) increases. Test (Source) decreases.
                        $billableValue = $detail->new_target_quantity;
                        $testValue = $detail->new_source_quantity;
                    } else {
                        // Billable -> Test
                        // Billable (Source) decreases. Test (Target) increases.
                        $billableValue = $detail->new_source_quantity;
                        $testValue = $detail->new_target_quantity;
                    }
                @endphp
                <tr>
                    <td style="border:1px solid #ddd; background:#ffffff;">
                        <strong style="color: #333;">{{ $detail->service->service_name }}</strong>
                        @if($detail->service->unit) <span style="color: #666; font-size: 11px;">({{ $detail->service->unit }})</span> @endif
                    </td>
                    <td style="border:1px solid #ddd; background:#ffffff; color: #666;">{{ $billableValue }} {{ $detail->service->unit }}</td>
                    <td style="border:1px solid #ddd; background:#ffffff; text-align:center; color:#0d6efd; font-weight:bold;">{{ $detail->transfer_amount }} {{ $detail->service->unit }}</td>
                    <td style="border:1px solid #ddd; background:#ffffff; color: #7c3aed; font-weight:bold;">{{ $testValue }} {{ $detail->service->unit }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    <div style="margin-top:25px; text-align:center;">
        @if($task)
        <a href="{{ route('billing-task-management.index', ['dtid' => $task->id, 'da' => 'view']) }}"
            style="
               display:inline-block;
               width: 220px;
               text-align: center;
               margin: 10px 5px;
               padding:12px 22px;
               background-color:#0d6efd;
               color:#ffffff;
               text-decoration:none;
               font-size:16px;
               font-weight:bold;
               border-radius:4px;
           ">
            🔍 View Transfer
        </a>
        <a href="{{ url('/billing-task-management/' . $task->id . '/bill') }}"
            style="
               display:inline-block;
               width: 220px;
               text-align: center;
               margin: 10px 5px;
               padding:12px 22px;
               background-color:#16a34a;
               color:#ffffff;
               text-decoration:none;
               font-size:16px;
               font-weight:bold;
               border-radius:4px;
           ">
            📋 Bill
        </a>
        @endif
        <p style="margin-top: 30px; font-size: 0.9em; color: #666;">
            This is an automated notification from Mir Cloud Management System.
        </p>
    </div>
</body>
</html>
