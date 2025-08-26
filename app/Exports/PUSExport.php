<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\DataDewasa;
use App\Models\AbsenDewasa;
use App\Models\TanggalAktif;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;

class PUSExport extends StringValueBinder implements FromCollection, WithHeadings, WithStyles, WithCustomStartCell, WithCustomValueBinder
{
    protected $data;

    /**
     * Ambil data sekali aja (cache di property)
     */
     public function collection()
    {
        $tanggalAktif = TanggalAktif::first()->tanggal;

        return AbsenDewasa::whereDate('tanggal_absen', $tanggalAktif)
            ->where('note', 'PUS')  // Add PUS filter
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
                    return number_format((float)$value, 1, '.', '');
                };

                // Format dates properly
                $tanggalAktif = Carbon::parse($item->tanggal_absen)->format('d/m/Y');
                $tanggalLahir = $item->tanggal_lahir ? Carbon::parse($item->tanggal_lahir)->format('d/m/Y') : '';

                return [
                    $tanggalAktif,
                    $item->nik,
                    $item->nama,
                    $tanggalLahir,
                    $item->usia,
                    $alamat,
                    $formatNumber($item->bb),
                    $formatNumber($item->tb),
                    $formatNumber($item->lp),
                    $formatNumber($item->lila),
                    $formatNumber($item->sistole),
                    $formatNumber($item->diastole),
                    $formatNumber($item->au),
                    $formatNumber($item->gda),
                    $formatNumber($item->kol),
                    $item->ket === 'KOSONG' ? '' : $item->ket
                ];
            });
    }

    /**
     * Helper format angka
     */
    public function startCell(): string
    {
        return 'A4'; // headings dimulai di row 4
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = 4 + $this->collection()->count(); // headings mulai row 4

        // Kolom widths
        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(35);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(8);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(8);
        $sheet->getColumnDimension('H')->setWidth(8);
        $sheet->getColumnDimension('I')->setWidth(8);
        $sheet->getColumnDimension('J')->setWidth(8);
        $sheet->getColumnDimension('K')->setWidth(10);
        $sheet->getColumnDimension('L')->setWidth(10);
        $sheet->getColumnDimension('M')->setWidth(8);
        $sheet->getColumnDimension('N')->setWidth(8);
        $sheet->getColumnDimension('O')->setWidth(8);
        $sheet->getColumnDimension('P')->setWidth(20);

        // Judul
        $sheet->mergeCells('A1:P1');
        $sheet->setCellValue('A1', 'POSYANDU ILP JERUK');
        $sheet->mergeCells('A2:P2');
        $sheet->setCellValue('A2', 'LAPORAN BULANAN KEHADIRAN PASANGAN USIA SUBUR');

        // Style judul
        $sheet->getStyle('A1:P2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
        ]);
        $sheet->getStyle('O3:P3')->applyFromArray([
            'font' => ['size' => 11],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT]
        ]);

        // Header row (row 4)
        $sheet->getStyle('A4:P4')->applyFromArray([
            'font' => ['bold' => true, 'size' => 11],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E2E2E2']
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
        ]);

        // Data rows
        $sheet->getStyle('A5:P' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
        ]);

        // Row heights
        $sheet->getRowDimension(1)->setRowHeight(30);
        $sheet->getRowDimension(2)->setRowHeight(30);
        $sheet->getRowDimension(4)->setRowHeight(35);
        for ($i = 5; $i <= $lastRow; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(25);
        }

        // Outer border
        $sheet->getStyle('A1:P' . $lastRow)->applyFromArray([
            'borders' => [
                'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]
            ]
        ]);

        // Footer
        $footerStart = $lastRow + 2;
        $sheet->mergeCells('A' . $footerStart . ':P' . $footerStart);
        $sheet->getRowDimension($footerStart)->setRowHeight(10);

        $sheet->mergeCells('O' . ($footerStart + 1) . ':P' . ($footerStart + 1));
        $sheet->setCellValue('O' . ($footerStart + 1), 'Mengetahui,');

        $sheet->mergeCells('O' . ($footerStart + 2) . ':P' . ($footerStart + 2));
        $sheet->setCellValue('O' . ($footerStart + 2), 'Ketua Posyandu');

        $sheet->getRowDimension($footerStart + 3)->setRowHeight(40);

        $sheet->mergeCells('O' . ($footerStart + 4) . ':P' . ($footerStart + 4));
        $sheet->setCellValue('O' . ($footerStart + 4), '(NOER CHASANAH)');

        for ($row = $footerStart + 1; $row <= $footerStart + 4; $row++) {
            if ($row != $footerStart + 3) {
                $sheet->getStyle('O' . $row . ':P' . $row)->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'font' => ['size' => 11]
                ]);
            }
        }

        $sheet->getStyle('O' . ($footerStart + 4) . ':P' . ($footerStart + 4))->applyFromArray([
            'borders' => [
                'top' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]
            ]
        ]);

        $sheet->getStyle('A1:P' . ($footerStart + 4))->applyFromArray([
            'borders' => [
                'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]
            ]
        ]);

        // Format khusus kolom NIK jadi text (biar leading zero gak hilang)
        $sheet->getStyle('B5:B' . $lastRow)->getNumberFormat()
            ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);

        return $sheet;
    }

    public function headings(): array
    {
        return [
            'TANGGAL',
            'NIK',
            'NAMA',
            'TANGGAL LAHIR',
            'USIA',
            'ALAMAT',
            'BB',
            'TB',
            'LP',
            'LILA',
            'SISTOLE',
            'DIASTOLE',
            'AU',
            'GDA',
            'KOL',
            'KETERANGAN'
        ];
    }
}

