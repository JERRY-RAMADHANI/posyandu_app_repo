<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\DataDewasa;
use App\Models\AbsenDewasa;
use App\Models\TanggalAktif;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\FromCollection;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;

class AbsenDewasaExport implements FromCollection, WithHeadings, WithStyles, WithCustomStartCell, WithDrawings
{
    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo Sidoarjo');
        $drawing->setPath(public_path('/sound/sidoarjo-black-white-seeklogo.png'));
        $drawing->setHeight(80);
        $drawing->setWidth(80);
        $drawing->setOffsetX(80);
        $drawing->setOffsetY(20);
        $drawing->setCoordinates('A1');

        return $drawing;
    }



    public function collection()
    {
        $tanggalAktif = TanggalAktif::first()->tanggal;

        return AbsenDewasa::whereDate('tanggal_absen', $tanggalAktif)
            ->get()
            ->map(function ($item, $index) {
                $dataDewasa = DataDewasa::where('no_reg', $item->no_reg)->first();
                $alamat = "RT {$dataDewasa->rt} RW {$dataDewasa->rw}";

                // Helper function to format numbers
                $formatNumber = function ($value) {
                    if (
                        $value === null
                        || $value === ''
                        || (is_string($value) && strtoupper($value) === 'KOSONG')
                        || (is_numeric($value) && (float)$value == 0)
                    ) {
                        return '';
                    }

                    return " " . sprintf("%.1f", (float)$value);
                };

                // Format tensi as sistole/diastole
                $tensi = $item->sistole && $item->diastole ?
                    $item->sistole . '/' . $item->diastole : '';

                return [
                    $index + 1,
                    $item->nama,
                    $alamat,
                    $item->usia,
                    $formatNumber($item->bb),
                    $formatNumber($item->tb),
                    $formatNumber($item->lp),
                    $tensi,
                    $formatNumber($item->au),
                    $formatNumber($item->gda),
                    $formatNumber($item->kol)
                ];
            });
    }

    public function headings(): array
    {
        return [
            'NO.',
            'NAMA',
            'ALAMAT',
            'USIA',
            'BB',
            'TB',
            'LP',
            'TENSI',
            'AU',
            'GDA',
            'KOL'
        ];
    }

    public function startCell(): string
    {
        return 'A10';
    }

    public function styles(Worksheet $sheet)
    {
        $tanggalAktif = TanggalAktif::first()->tanggal;
        $tanggal = Carbon::parse($tanggalAktif);

        Carbon::setLocale('id');

        // Update column widths
        $sheet->getColumnDimension('A')->setWidth(4);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(7);
        $sheet->getColumnDimension('E')->setWidth(7);
        $sheet->getColumnDimension('F')->setWidth(7);
        $sheet->getColumnDimension('G')->setWidth(7);
        $sheet->getColumnDimension('H')->setWidth(8);  // Slightly wider for TENSI
        $sheet->getColumnDimension('I')->setWidth(7);
        $sheet->getColumnDimension('J')->setWidth(7);
        $sheet->getColumnDimension('K')->setWidth(7);

        // Header text
        $sheet->mergeCells('B1:K1');
        $sheet->mergeCells('B2:K2');
        $sheet->mergeCells('B3:K3');
        $sheet->mergeCells('A4:K4');

        $sheet->setCellValue('B1', 'PEMERINTAH KABUPATEN SIDOARJO');
        $sheet->setCellValue('B2', 'KECAMATAN SEDATI');
        $sheet->setCellValue('B3', 'D E S A  P A B E A N');
        $sheet->setCellValue('A4', 'Jalan Abd. Rahman no. 02 Desa Pabean No. Telp. 031-99680895');

        // Header styling
        $sheet->getStyle('B1:K3')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true
            ]
        ]);

        // Address line with single thin underline
        $sheet->getStyle('A4')->applyFromArray([
            'borders' => [
                'bottom' => ['borderStyle' => Border::BORDER_THIN]
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'font' => ['color' => ['rgb' => '000000']] // Changed to black
        ]);

        // Title with proper spacing
        $sheet->mergeCells('A5:K5');
        $sheet->setCellValue('A5', 'DAFTAR HADIR POSYANDU DESA PABEAN');
        $sheet->getStyle('A5')->applyFromArray([
            'font' => ['bold' => true, 'underline' => true, 'size' => 12],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        // Info section with proper spacing
        $sheet->setCellValue('A6', '    Nama Posyandu : Jeruk');
        $sheet->setCellValue('A7', '    Hari/Tanggal       : ' . $tanggal->isoFormat('dddd[/]DD MMMM YYYY'));
        $sheet->setCellValue('A8', '    Tempat                : Perum. Sedati Permai Jl. Mliwis RW-13');

        // Table headers with thin borders only
        $sheet->getStyle('A10:K10')->applyFromArray([
            'font' => ['bold' => true],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        // Table body with thin borders
        $lastRow = 10 + $this->collection()->count();
        $sheet->getStyle('A11:K' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);

        // Footer section without borders
        $footerStart = $lastRow + 1;

        // // Left footer
        // $sheet->setCellValue('A' . $footerStart, '1. Jumlah Balita Usia 0-24 Bulan');
        // $sheet->setCellValue('A' . ($footerStart + 1), '2. Jumlah Balita Usia 25-60 Bulan');
        // $sheet->setCellValue('A' . ($footerStart + 2), '3. Jumlah Ibu Hamil');
        // $sheet->setCellValue('A' . ($footerStart + 3), '4. Jumlah Bayi Lahir');
        // $sheet->getStyle('A' . $footerStart . ':A' . ($footerStart + 3))->getFont()->setSize(11);

        // Right footer
        $sheet->mergeCells('I' . $footerStart . ':K' . $footerStart);
        $sheet->setCellValue('I' . $footerStart, 'Mengetahui,');
        $sheet->getStyle('I' . $footerStart . ':K' . $footerStart)->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER
            ]
        ]);

        $sheet->mergeCells('I' . ($footerStart + 1) . ':K' . ($footerStart + 1));
        $sheet->setCellValue('I' . ($footerStart + 1), 'Ketua Posyandu');
        $sheet->getStyle('I' . ($footerStart + 1) . ':K' . ($footerStart + 1))->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER
            ]
        ]);

        // Signature
        $sheet->mergeCells('I' . ($footerStart + 5) . ':K' . ($footerStart + 5));
        $sheet->setCellValue('I' . ($footerStart + 5), '(NOER CHASANAH)');
        $sheet->getStyle('I' . ($footerStart + 5) . ':K' . ($footerStart + 5))->applyFromArray([
            'borders' => [
                'top' => ['borderStyle' => Border::BORDER_THIN]
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER
            ]
        ]);

        // Tinggi baris "Mengetahui,"
        $sheet->getRowDimension($footerStart)->setRowHeight(20);

        // Tinggi baris "Ketua Posyandu"
        $sheet->getRowDimension($footerStart + 1)->setRowHeight(20);

        // Kasih space buat tanda tangan (lebih tinggi)
        $sheet->getRowDimension($footerStart + 2)->setRowHeight(20);
        $sheet->getRowDimension($footerStart + 3)->setRowHeight(20);
        $sheet->getRowDimension($footerStart + 4)->setRowHeight(20);

        // Tinggi baris tanda tangan
        $sheet->getRowDimension($footerStart + 5)->setRowHeight(20);


        // Add proper spacing
        $sheet->getRowDimension(1)->setRowHeight(20);
        $sheet->getRowDimension(2)->setRowHeight(20);
        $sheet->getRowDimension(3)->setRowHeight(20);
        $sheet->getRowDimension(4)->setRowHeight(20);
        $sheet->getRowDimension(5)->setRowHeight(25);
        $sheet->getRowDimension(6)->setRowHeight(20);
        $sheet->getRowDimension(7)->setRowHeight(20);
        $sheet->getRowDimension(8)->setRowHeight(20);
        $sheet->getRowDimension(10)->setRowHeight(25);

        // Set row height for table content (main section)
        $lastRow = 10 + $this->collection()->count();
        for ($row = 11; $row <= $lastRow; $row++) {
            $sheet->getRowDimension($row)->setRowHeight(30); // Ubah angka 30 sesuai kebutuhan
        }

        // Add outer border for entire report
        $lastFooterRow = $footerStart + 5;
        $sheet->getStyle('A1:K' . $lastFooterRow)->applyFromArray([
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        // Header section styling with border separator
        $sheet->getStyle('A4:K4')->applyFromArray([
            'borders' => [
                'bottom' => ['borderStyle' => Border::BORDER_THIN],
            ],
        ]);

        // Table header with gray background
        $sheet->getStyle('A10:K10')->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'D3D3D3'], // Light gray background
            ],
            'font' => ['bold' => true],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
        ]);

        // Add border separator before footer
        // $sheet->getStyle('A' . ($lastRow + 1) . ':J' . ($lastRow + 1))->applyFromArray([
        //     'borders' => [
        //         'bottom' => ['borderStyle' => Border::BORDER_THIN],
        //     ],
        // ]);

        return $sheet;
    }
}
