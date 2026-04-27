<?php

namespace App\Exports;

use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;
use PHPExcel_Style_Fill;
use Illuminate\Support\Str;

class ExpenseReportExport
{
    protected array $payload;

    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }

    public function download(string $fileName)
    {
        return response()->streamDownload(function () use ($fileName) {
            $spreadsheet = $this->buildSpreadsheet();
            $writer = PHPExcel_IOFactory::createWriter($spreadsheet, 'Excel2007');
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    protected function buildSpreadsheet()
    {
        $spreadsheet = new PHPExcel();
        $sheet = $spreadsheet->getActiveSheet();
        $sheetName = Str::limit($this->payload['sheetName'], 31);
        $sheet->setTitle($sheetName);

        $sheet->mergeCells('A1:G1');
        $sheet->setCellValue('A1', $this->payload['sheetName']);
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER],
            'fill' => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => ['rgb' => '1E3A5F']],
        ]);

        $sheet->setCellValue('A2', 'Report Range: '.$this->payload['rangeLabel']);
        $sheet->mergeCells('A2:G2');
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['italic' => true],
            'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER],
        ]);

        $row = 4;
        $sheet->mergeCells("A{$row}:G{$row}");
        $sheet->setCellValue("A{$row}", 'Daily Expenses');
        $sheet->getStyle("A{$row}")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => ['rgb' => 'F0F0F0']],
        ]);

        $row += 1;
        $headers = ['Date', 'Milk', 'Water', 'Category Name', 'Amount', 'Category', 'Total Day Expense'];
        $sheet->fromArray($headers, null, "A{$row}");
        $sheet->getStyle("A{$row}:G{$row}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => ['rgb' => '1E3A5F']],
            'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER],
        ]);

        $row += 1;
        foreach ($this->payload['dailyRows'] as $item) {
            $sheet->setCellValue("A{$row}", $item['date']);
            $sheet->setCellValue("B{$row}", $item['milk']);
            $sheet->setCellValue("C{$row}", $item['water']);
            $sheet->setCellValueExplicit("D{$row}", $item['category_name'], PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->setCellValue("E{$row}", $item['amount']);
            $sheet->setCellValue("F{$row}", $item['category']);
            $sheet->setCellValue("G{$row}", $item['total_day_expense']);

            $sheet->getStyle("B{$row}:C{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle("E{$row}:G{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("D{$row}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $row++;
        }

        $row += 1;
        $sheet->mergeCells("A{$row}:G{$row}");
        $sheet->setCellValue("A{$row}", 'Weekly Grocery Summary');
        $sheet->getStyle("A{$row}")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => ['rgb' => 'F0F0F0']],
        ]);

        $row += 1;
        $sheet->fromArray(['Week', 'Start Date', 'End Date', 'Total Grocery', 'Per Member Deduction'], null, "A{$row}");
        $sheet->getStyle("A{$row}:E{$row}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => ['rgb' => '1E3A5F']],
            'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER],
        ]);

        $row += 1;
        foreach ($this->payload['weeklySummary'] as $week) {
            $sheet->setCellValue("A{$row}", 'Week '.$week['week']);
            $sheet->setCellValue("B{$row}", $week['start_date']);
            $sheet->setCellValue("C{$row}", $week['end_date']);
            $sheet->setCellValue("D{$row}", $week['total_grocery']);
            $sheet->setCellValue("E{$row}", $week['per_member_deduction']);
            $sheet->getStyle("D{$row}:E{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle("A{$row}:E{$row}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $row++;
        }

        $row += 1;
        $sheet->mergeCells("A{$row}:G{$row}");
        $sheet->setCellValue("A{$row}", 'Member Statement');
        $sheet->getStyle("A{$row}")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => ['rgb' => 'F0F0F0']],
        ]);

        $row += 1;
        $sheet->fromArray(['Member Name', 'Total Assigned', 'Weekly Deductions', 'Total Paid', 'Remaining', 'Status'], null, "A{$row}");
        $sheet->getStyle("A{$row}:F{$row}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => ['rgb' => '1E3A5F']],
            'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER],
        ]);

        $row += 1;
        foreach ($this->payload['members'] as $member) {
            $sheet->setCellValueExplicit("A{$row}", $member['name'], PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->setCellValue("B{$row}", $member['total_assigned']);
            $sheet->setCellValueExplicit("C{$row}", $member['weekly_deductions'], PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->setCellValue("D{$row}", $member['paid_amount']);
            $sheet->setCellValue("E{$row}", $member['remaining']);
            $sheet->setCellValue("F{$row}", ucfirst($member['status']));

            $sheet->getStyle("B{$row}:E{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle("F{$row}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $statusColor = match (strtolower($member['status'])) {
                'paid' => '28A745',
                'partial' => 'FFC107',
                'unpaid' => 'DC3545',
                default => 'F0F0F0',
            };

            $sheet->getStyle("F{$row}")->applyFromArray([
                'fill' => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => ['rgb' => $statusColor]],
                'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true],
            ]);
            $row++;
        }

        $row += 1;
        $sheet->mergeCells("A{$row}:G{$row}");
        $sheet->setCellValue("A{$row}", 'Final Balance Sheet');
        $sheet->getStyle("A{$row}")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => ['rgb' => 'F0F0F0']],
        ]);

        $row += 1;
        foreach ([
            'Total Grocery Expenses (All Weeks)' => $this->payload['financialSummary']['total_grocery_expenses'],
            'Total Member Collection' => $this->payload['financialSummary']['total_member_collection'],
            'Remaining Balance' => $this->payload['financialSummary']['remaining_balance'],
            'Extra Balance' => $this->payload['financialSummary']['extra_balance'],
        ] as $label => $amount) {
            $sheet->setCellValue("A{$row}", $label);
            $sheet->setCellValue("B{$row}", $amount);
            $sheet->getStyle("B{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle("A{$row}:B{$row}")->applyFromArray([
                'font' => ['bold' => true],
                'fill' => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => ['rgb' => 'FFF3CD']],
            ]);
            $row++;
        }

        foreach (range('A', 'G') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $sheet->freezePane('A5');
        $lastRow = max(4, $row - 1);
        $sheet->getStyle("A1:G{$lastRow}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

        return $spreadsheet;
    }
}
