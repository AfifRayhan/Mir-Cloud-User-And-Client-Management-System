<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; }
        .details-box { background-color: #f9f9f9; padding: 15px; border: 1px solid #ddd; border-radius: 5px; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>New Resource Recommendation</h2>
    <p>A new resource {{ $actionType }} recommendation has been submitted by {{ $sender->name }}.</p>
    
    <div class="details-box">
        <p><strong>Customer:</strong> {{ $task->customer->customer_name }} ({{ $task->customer->customer_id }})</p>
        <p><strong>Platform:</strong> {{ optional($task->customer->platform)->platform_name ?? 'N/A' }}</p>
        <p><strong>Activation Date:</strong> {{ $task->activation_date->format('M d, Y') }}</p>
        <p><strong>Type:</strong> {{ ucfirst($actionType) }}</p>
    </div>

    <h3>Resource Details</h3>
    @if($task->allocation_type === 'upgrade' && $task->resourceUpgradation)
        <table>
            <thead>
                <tr>
                    <th>Service</th>
                    <th>Current Value</th>
                    <th>Upgrade Value</th>
                </tr>
            </thead>
            <tbody>
                @foreach($task->resourceUpgradation->details as $detail)
                    <tr>
                        <td>{{ $detail->service->service_name }}</td>
                        <td>{{ $detail->quantity }}</td>
                        <td>{{ $detail->upgrade_amount }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @elseif($task->allocation_type === 'downgrade' && $task->resourceDowngradation)
        <table>
            <thead>
                <tr>
                    <th>Service</th>
                    <th>Current Value</th>
                    <th>Downgrade Value</th>
                </tr>
            </thead>
            <tbody>
                @foreach($task->resourceDowngradation->details as $detail)
                    <tr>
                        <td>{{ $detail->service->service_name }}</td>
                        <td>{{ $detail->quantity }}</td>
                        <td>{{ $detail->downgrade_amount }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No specific resource details available.</p>
    @endif
</body>
</html>
