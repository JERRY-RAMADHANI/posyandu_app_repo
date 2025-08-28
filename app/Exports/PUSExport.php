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
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Concerns\WithDrawings;

class PUSExport extends StringValueBinder implements FromCollection, WithHeadings, WithStyles, WithCustomStartCell, WithCustomValueBinder, WithDrawings
{
    protected $data;

    /**
     * Ambil data sekali aja (cache di property)
     */
    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo Sidoarjo');
        $drawing->setPath(public_path('/sound/sidoarjo-black-white-seeklogo.png'));
        $drawing->setHeight(100);
        $drawing->setWidth(100);
        $drawing->setOffsetX(50); // Reset offset kecil
        $drawing->setOffsetY(15);
        $drawing->setCoordinates('C1'); // Pindah ke kolom C

        return $drawing;
    }

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
                    $item->ket
                ];
            });
    }

    /**
     * Helper format angka
     */
    public function startCell(): string
    {
        return 'A10'; // Sama seperti AbsenDewasaExport
    }

    public function styles(Worksheet $sheet)
    {
        $tanggalAktif = TanggalAktif::first()->tanggal;
        $tanggal = Carbon::parse($tanggalAktif);

        Carbon::setLocale('id');

        // Update column widths untuk PUS (16 kolom A-P)
        $sheet->getColumnDimension('A')->setWidth(15); // Kasih ruang untuk logo
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

        // Header text - FULL WIDTH CENTER (A1 sampai P4)
        $sheet->mergeCells('A1:P1'); // Full width termasuk kolom logo
        $sheet->mergeCells('A2:P2');
        $sheet->mergeCells('A3:P3');
        $sheet->mergeCells('A4:P4');

        $sheet->setCellValue('A1', 'PEMERINTAH KABUPATEN SIDOARJO');
        $sheet->setCellValue('A2', 'KECAMATAN SEDATI');
        $sheet->setCellValue('A3', 'D E S A  P A B E A N');
        $sheet->setCellValue('A4', 'Jalan Abd. Rahman no. 02 Desa Pabean No. Telp. 031-99680895');

        // Header styling - PERFECT CENTER
        $sheet->getStyle('A1:P3')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true
            ]
        ]);

        // Address line with single thin underline
        $sheet->getStyle('A4:P4')->applyFromArray([
            'borders' => [
                'bottom' => ['borderStyle' => Border::BORDER_THIN]
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'font' => ['color' => ['rgb' => '000000']]
        ]);

        // Title with proper spacing - ubah judul untuk PUS
        $sheet->mergeCells('A5:P5');
        $sheet->setCellValue('A5', 'LAPORAN BULANAN KEHADIRAN PASANGAN USIA SUBUR');
        $sheet->getStyle('A5')->applyFromArray([
            'font' => ['bold' => true, 'underline' => true, 'size' => 12],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);

        // Info section with proper spacing
        $sheet->setCellValue('A6', '    Nama Posyandu : Jeruk');
        $sheet->setCellValue('A7', '    Hari/Tanggal       : ' . $tanggal->isoFormat('dddd[/]DD MMMM YYYY'));
        $sheet->setCellValue('A8', '    Tempat                : Perum. Sedati Permai Jl. Mliwis RW-13');

        // Table headers dengan kolom A-P
        $sheet->getStyle('A10:P10')->applyFromArray([
            'font' => ['bold' => true],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);

        // Table body dengan kolom A-P
        $lastRow = 10 + $this->collection()->count();
        $sheet->getStyle('A11:P' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);

        // Footer section - TAMBAH JARAK
        $footerStart = $lastRow + 3; // Ubah dari +1 ke +3 untuk kasih jarak

        // Right footer
        $sheet->mergeCells('N' . $footerStart . ':P' . $footerStart);
        $sheet->setCellValue('N' . $footerStart, 'Mengetahui,');
        $sheet->getStyle('N' . $footerStart . ':P' . $footerStart)->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER
            ]
        ]);

        $sheet->mergeCells('N' . ($footerStart + 1) . ':P' . ($footerStart + 1));
        $sheet->setCellValue('N' . ($footerStart + 1), 'Ketua Posyandu');
        $sheet->getStyle('N' . ($footerStart + 1) . ':P' . ($footerStart + 1))->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER
            ]
        ]);

        // Signature - TANPA GARIS
        $sheet->mergeCells('N' . ($footerStart + 5) . ':P' . ($footerStart + 5));
        $sheet->setCellValue('N' . ($footerStart + 5), '(NOER CHASANAH)');
        $sheet->getStyle('N' . ($footerStart + 5) . ':P' . ($footerStart + 5))->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER
            ]
        ]);

        // Row heights untuk header yang cukup tinggi untuk logo
        $sheet->getRowDimension(1)->setRowHeight(25);
        $sheet->getRowDimension(2)->setRowHeight(25);
        $sheet->getRowDimension(3)->setRowHeight(25);
        $sheet->getRowDimension(4)->setRowHeight(25);
        $sheet->getRowDimension(5)->setRowHeight(30);
        $sheet->getRowDimension(6)->setRowHeight(20);
        $sheet->getRowDimension(7)->setRowHeight(20);
        $sheet->getRowDimension(8)->setRowHeight(20);
        $sheet->getRowDimension(10)->setRowHeight(25);

        // Footer row heights
        $sheet->getRowDimension($footerStart)->setRowHeight(20);
        $sheet->getRowDimension($footerStart + 1)->setRowHeight(20);
        $sheet->getRowDimension($footerStart + 2)->setRowHeight(20);
        $sheet->getRowDimension($footerStart + 3)->setRowHeight(20);
        $sheet->getRowDimension($footerStart + 4)->setRowHeight(20);
        $sheet->getRowDimension($footerStart + 5)->setRowHeight(20);

        // Set row height untuk table content
        for ($row = 11; $row <= $lastRow; $row++) {
            $sheet->getRowDimension($row)->setRowHeight(30);
        }

        // Add outer border untuk entire report
        $lastFooterRow = $footerStart + 5;
        $sheet->getStyle('A1:P' . $lastFooterRow)->applyFromArray([
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        // Table header dengan gray background
        $sheet->getStyle('A10:P10')->applyFromArray([
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

        // Format khusus kolom NIK jadi text (biar leading zero gak hilang)
        $sheet->getStyle('B11:B' . $lastRow)->getNumberFormat()
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

