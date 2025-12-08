<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Resource Allocation Summary</h2>
    <p>The following task has been completed by {{ $sender->name }}.</p>

    <h3>Task Details</h3>
    <table>
        <tr>
            <th>Platform</th>
            <td>{{ optional($task->customer->platform)->platform_name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Customer</th>
            <td>{{ $task->customer->customer_name }} ({{ $task->customer->customer_id }})</td>
        </tr>
        <tr>
            <th>Allocation Type</th>
            <td>{{ ucfirst($task->allocation_type) }}</td>
        </tr>
        <tr>
            <th>Activation Date</th>
            <td>{{ $task->activation_date->format('M d, Y') }}</td>
        </tr>
    </table>

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
