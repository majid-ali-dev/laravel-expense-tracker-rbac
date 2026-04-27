<?php

namespace App\Exports;

use App\Models\Expense;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExpenseSheetExport implements FromArray, ShouldAutoSize, WithHeadings, WithStyles
{
    protected $expenses;

    protected $members;

    public function __construct()
    {
        $this->expenses = Expense::with('user')->latest('date')->get();
        $this->members = User::whereHas('roles', function ($q) {
            $q->where('name', 'member');
        })->get();
    }

    public function array(): array
    {
        $data = [];

        // Add title row
        $data[] = ['March - Saved'];
        $data[] = [];

        // Headers will be added separately via WithHeadings

        // Add expense rows
        foreach ($this->expenses as $expense) {
            $isMilk = str_contains(strtolower($expense->title), 'milk');
            $isWater = str_contains(strtolower($expense->title), 'water');

            $data[] = [
                $expense->date->format('d/m/Y'),
                $isMilk ? $expense->amount : '',
                $isWater ? $expense->amount : '',
                ! $isMilk && ! $isWater ? $expense->title : '',
                ! $isMilk && ! $isWater ? $expense->amount : '',
                $expense->amount,
            ];
        }

        // Add empty row
        $data[] = [];

        // Add members section header
        $data[] = ['Members Summary'];
        $data[] = ['Member Name', 'Total Amount', 'Paid Amount', 'Remaining', 'Status'];

        // Add member rows
        foreach ($this->members as $member) {
            $data[] = [
                $member->name,
                number_format($member->total_amount ?? 0, 2),
                number_format($member->total_paid ?? 0, 2),
                number_format($member->remaining ?? 0, 2),
                ucfirst($member->payment_status ?? 'unpaid'),
            ];
        }

        // Add totals row
        $totalAmount = $this->members->sum('total_amount');
        $totalPaid = $this->members->sum('total_paid');
        $totalRemaining = $this->members->sum('remaining');

        $data[] = ['TOTAL', number_format($totalAmount, 2), number_format($totalPaid, 2), number_format($totalRemaining, 2), ''];

        return $data;
    }

    public function headings(): array
    {
        return [
            'Date', 'Milk', 'Water', 'Name', 'Amount', 'Total',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style the title row
        $sheet->mergeCells('A1:F1');
        $sheet->setCellValue('A1', 'March - Saved');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Style headers (row 3)
        $sheet->getStyle('A3:F3')->getFont()->setBold(true);
        $sheet->getStyle('A3:F3')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE0E0E0');

        // Get last row with data
        $highestRow = $sheet->getHighestRow();

        // Apply borders to all data cells
        $sheet->getStyle('A3:F'.$highestRow)
            ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Center align all data cells
        $sheet->getStyle('A3:F'.$highestRow)
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Style members summary header
        $membersRow = $this->expenses->count() + 5;
        $sheet->getStyle('A'.$membersRow.':E'.$membersRow)->getFont()->setBold(true);

        return [];
    }
}
