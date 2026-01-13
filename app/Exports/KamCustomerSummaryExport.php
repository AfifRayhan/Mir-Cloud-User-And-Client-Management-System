<?php

namespace App\Exports;

use App\Models\Customer;
use App\Models\Service;
use App\Models\Summary;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KamCustomerSummaryExport implements FromCollection, ShouldAutoSize, WithHeadings, WithStyles
{
    protected $customers;

    protected $allServices;

    public function __construct($customers)
    {
        $this->customers = $customers;

        // Get all unique services across the platforms of these customers
        $platformIds = $customers->pluck('platform_id')->unique();
        $this->allServices = Service::whereIn('platform_id', $platformIds)
            ->get()
            ->unique('service_name')
            ->values();
    }

    public function collection()
    {
        $rows = [];

        foreach ($this->customers as $customer) {
            $row = [
                'Customer ID' => $customer->id,
                'Customer Name' => $customer->customer_name,
                'Platform' => $customer->platform->platform_name ?? 'N/A',
            ];

            // Get summaries for this customer
            $summaries = Summary::where('customer_id', $customer->id)
                ->get()
                ->keyBy(function ($summary) {
                    return $summary->service->service_name;
                });

            foreach ($this->allServices as $service) {
                $summary = $summaries->get($service->service_name);
                $quantity = $summary ? ($summary->billable_quantity + $summary->test_quantity) : 0;
                $row[$service->service_name] = (int) $quantity;
            }

            $rows[] = $row;
        }

        return collect($rows);
    }

    public function headings(): array
    {
        $headings = [
            'Customer ID',
            'Customer Name',
            'Platform',
        ];

        foreach ($this->allServices as $service) {
            $headings[] = $service->service_name.($service->unit ? " ({$service->unit})" : '');
        }

        return $headings;
    }

    public function styles(Worksheet $sheet)
    {
        // Header styling
        $lastColumn = $this->getNameFromNumber(count($this->headings()));
        $headerRange = 'A1:'.$lastColumn.'1';

        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFDEEAF6');
        $sheet->getStyle($headerRange)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Add borders to all data rows
        $totalRows = count($this->customers) + 1;
        if ($totalRows > 1) {
            $sheet->getStyle('A2:'.$lastColumn.$totalRows)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        }

        return [];
    }

    protected function getNameFromNumber($num)
    {
        $index = $num;
        $columnName = '';
        while ($index > 0) {
            $modulo = ($index - 1) % 26;
            $columnName = chr(65 + $modulo).$columnName;
            $index = (int) (($index - $modulo) / 26);
        }

        return $columnName;
    }
}
