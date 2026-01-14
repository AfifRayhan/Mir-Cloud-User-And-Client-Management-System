<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KamTasksExport implements FromCollection, ShouldAutoSize, WithHeadings, WithStyles
{
    protected $tasks;

    protected $detailRows = [];

    protected $transferRows = [];

    public function __construct($tasks)
    {
        $this->tasks = $tasks;
    }

    public function collection()
    {
        $rows = [];
        $testStatusId = \App\Models\CustomerStatus::where('name', 'Test')->first()?->id ?? 2;
        $currentRow = 2; // Starting from row 2 because of headings

        foreach ($this->tasks as $task) {
            $isTest = $task->status_id == $testStatusId;
            $completionStatus = 'Pending';
            if ($task->completed_at) {
                $completionStatus = 'Completed';
            } elseif ($task->assigned_to) {
                $completionStatus = 'Assigned';
            }

            // Get complete resource details
            $allServices = \App\Models\Service::where('platform_id', $task->customer->platform_id)->get();
            $existingDetails = $task->resourceDetails;
            $detailsMap = $existingDetails->keyBy('service_id');

            $resourceDetails = $allServices->map(function ($service) use ($detailsMap, $task, $testStatusId) {
                $existingDetail = $detailsMap->get($service->id);
                if ($existingDetail) {
                    return $existingDetail;
                }

                // Default empty detail object
                $default = (object) [
                    'service' => $service,
                    'service_id' => $service->id,
                ];

                if ($task->allocation_type === 'transfer') {
                    $default->transfer_amount = 0;
                    $default->current_source_quantity = $task->status_id == $testStatusId
                        ? $task->customer->getResourceTestQuantity($service->service_name)
                        : $task->customer->getResourceBillableQuantity($service->service_name);
                    $default->new_target_quantity = $default->current_source_quantity;
                } else {
                    $default->upgrade_amount = 0;
                    $default->downgrade_amount = 0;
                    $default->quantity = $task->status_id == $testStatusId
                        ? $task->customer->getResourceTestQuantity($service->service_name)
                        : $task->customer->getResourceBillableQuantity($service->service_name);
                }

                return $default;
            });

            $firstRow = true;
            foreach ($resourceDetails as $detail) {
                $isUpgrade = $task->allocation_type === 'upgrade';
                $isTransfer = $task->allocation_type === 'transfer';

                if ($isTransfer) {
                    $amount = $detail->transfer_amount ?? 0;
                    $prev = $detail->current_source_quantity ?? 0;
                    $next = $detail->new_target_quantity ?? 0;
                    $this->transferRows[] = $currentRow;
                } else {
                    $amount = $isUpgrade ? ($detail->upgrade_amount ?? 0) : ($detail->downgrade_amount ?? 0);
                    $next = $detail->quantity ?? 0;
                    $prev = $isUpgrade ? ($next - $amount) : ($next + $amount);
                }

                $rows[] = [
                    'Customer' => $firstRow ? $task->customer->customer_name : '',
                    'Task ID' => $firstRow ? ($task->task_id ?? 'N/A') : '',
                    'Platform' => $firstRow ? ($task->customer->platform->platform_name ?? 'N/A') : '',
                    'Status' => $firstRow ? ($task->status->name ?? ($isTest ? 'Test' : 'Billable')) : '',
                    'Type' => $firstRow ? ucfirst($task->allocation_type) : '',
                    'Resource Assignment' => $firstRow ? ($task->assignment_datetime ? $task->assignment_datetime->format('Y-m-d H:i') : ($task->activation_date ? $task->activation_date->format('Y-m-d') : 'N/A')) : '',
                    'Resource Deadline' => $firstRow ? ($task->deadline_datetime ? $task->deadline_datetime->format('Y-m-d H:i') : 'N/A') : '',
                    'Completion Status' => $firstRow ? $completionStatus : '',
                    'Assigned To' => $firstRow ? ($task->assignedTo->name ?? 'Unassigned') : '',
                    'Service' => $detail->service->service_name.($detail->service->unit ? " ({$detail->service->unit})" : ''),
                    'Current' => (int) $prev,
                    'Change' => ($isTransfer ? '+ ' : ($amount > 0 ? ($isUpgrade ? '+ ' : '- ') : '')).$amount,
                    'New Total' => '→ '.(int) $next,
                ];

                $this->detailRows[] = $currentRow;
                $currentRow++;
                $firstRow = false;
            }

            // Empty separator row
            $rows[] = array_fill(0, 13, '');
            $currentRow++;
        }

        return collect($rows);
    }

    public function headings(): array
    {
        return [
            'Customer',
            'Task ID',
            'Platform',
            'Status',
            'Type',
            'Resource Assignment',
            'Resource Deadline',
            'Completion Status',
            'Assigned To',
            'Service',
            'Current',
            'Increase By/Reduce By',
            'New Total',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Header styling
        $headerRange = 'A1:M1';
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFDEEAF6');
        $sheet->getStyle($headerRange)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Apply stripes/borders/colors to detail sections
        foreach ($this->detailRows as $row) {
            // Service column (J) - Light Blue background tint
            $sheet->getStyle('J'.$row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFE9EEF5');
            // Current column (K) - Light Gray
            $sheet->getStyle('K'.$row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFF2F2F2');

            // Increase/Reduce column (L) - Custom
            $val = (string) $sheet->getCell('L'.$row)->getValue();

            if (in_array($row, $this->transferRows)) {
                // Transfer row - Light solid blue (per image)
                $sheet->getStyle('L'.$row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFD9E1F2'); // Light Blue/Purple
                $sheet->getStyle('L'.$row)->getFont()->getColor()->setARGB('FF305496'); // Dark Blue text
            } elseif (str_contains($val, '+')) {
                $sheet->getStyle('L'.$row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFE2F0D9'); // Light Green
                $sheet->getStyle('L'.$row)->getFont()->getColor()->setARGB('FF385623');
            } elseif (str_contains($val, '-')) {
                $sheet->getStyle('L'.$row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFEBD6'); // Light Orange
                $sheet->getStyle('L'.$row)->getFont()->getColor()->setARGB('FF974706');
            }

            // New Total column (M) - Cyan-ish
            $sheet->getStyle('M'.$row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFDAE3F3');
            $sheet->getStyle('M'.$row)->getFont()->setBold(true);

            // Borders for the "table" part (J to M)
            $sheet->getStyle('J'.$row.':M'.$row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        }

        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
