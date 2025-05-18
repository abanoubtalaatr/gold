<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class UserActivityExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, ShouldAutoSize
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'رقم المستخدم',
            'الاسم الكامل',
            'البريد الإلكتروني',
            'تاريخ التسجيل',
            'آخر دخول',
            'نوع المستخدم',
            'الحالة',
            'عدد العقود',
            'خطة الاشتراك'
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,  // رقم المستخدم
            'B' => 30,  // الاسم الكامل
            'C' => 40,  // البريد الإلكتروني
            'D' => 20,  // تاريخ التسجيل
            'E' => 20,  // آخر دخول
            'F' => 15,  // نوع المستخدم
            'G' => 15,  // الحالة
            'H' => 15,  // عدد العقود
            'I' => 20,  // خطة الاشتراك
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->setRightToLeft(true);

        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E5E7EB']
                ]
            ],
            'A1:I1' => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ],
            'A:I' => [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                ],
            ],
        ];
    }

    public function map($row): array
    {
        return [
            $row['user_id'],
            $row['full_name'],
            $row['email'],
            $row['registration_date'],
            $row['last_login'],
            $row['user_type'],
            $row['status'],
        ];
    }
}