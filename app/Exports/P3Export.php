<?php

namespace App\Exports;

use App\Models\DataBalita;
use App\Models\AbsenBalita;
use App\Models\TanggalAktif;
use Carbon\Carbon;
use Exception;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class P3Export implements FromCollection, WithHeadings, WithStyles, WithCustomStartCell, WithEvents
{
    protected $year;

    public function __construct()
    {
        $this->year = date('Y');
    }

    private function setupHeaders($sheet)
    {
        // Data Identitas kolom A-P
        $identitas = [
            'A' => "NIK\nLengkap\n(16 digit)",
            'B' => "Kode\nPROV\n(Dukacpil)",
            'C' => "Kode\nKab/Kota\n(Dukacpil)", 
            'D' => "Kode\nKecamatan\n(Dukacpil)",
            'E' => "Anak\nKe...",
            'F' => "BB\nLahir",
            'G' => "Panjang\nLahir\n(cm)",
            'H' => "NIK Anak\n(1 s/d 2\ndigit)",
            'I' => "Buku\nKIA",
            'J' => 'NAMA ORANG TUA',
            'K' => 'RT',
            'L' => 'RW',
            'M' => "NAMA\nPOSYANDU",
            'N' => 'NO',
            'O' => 'NAMA ANAK',
            'P' => "L/P\nL=1\nP=2",
        ];

        // Set identitas headers
        foreach ($identitas as $col => $text) {
            $sheet->mergeCells($col.'2:'.$col.'5');
            $sheet->setCellValue($col.'2', $text);
        }

        // TGL LAHIR - Q2:S2
        $sheet->mergeCells('Q2:S2');
        $sheet->setCellValue('Q2', 'TGL LAHIR');
        
        // Sub header TGL LAHIR
        $sheet->mergeCells('Q3:Q5'); $sheet->setCellValue('Q3', 'TGL');
        $sheet->mergeCells('R3:R5'); $sheet->setCellValue('R3', 'BLN');
        $sheet->mergeCells('S3:S5'); $sheet->setCellValue('S3', 'THN');

        // THN SEBELUMNYA - T2:W2 (4 kolom: T, U, V, W)
        $sheet->mergeCells('T2:W2');
        $sheet->setCellValue('T2', 'THN SEBELUMNYA');

        // Sub header THN SEBELUMNYA
        $sheet->mergeCells('T3:U3'); $sheet->setCellValue('T3', 'NOV');
        $sheet->mergeCells('V3:W3'); $sheet->setCellValue('V3', 'DES');

        // Sub-sub header untuk NOV dan DES
        $sheet->mergeCells('T4:T5'); $sheet->setCellValue('T4', 'BB');
        $sheet->mergeCells('U4:U5'); $sheet->setCellValue('U4', 'TB');
        $sheet->mergeCells('V4:V5'); $sheet->setCellValue('V4', 'BB');
        $sheet->mergeCells('W4:W5'); $sheet->setCellValue('W4', 'TB');

        // TANGGAL PENGUKURAN mulai dari X sampai AI (12 kolom: X-AI)
        $sheet->mergeCells('X2:AI2');
        $sheet->setCellValue('X2', 'TANGGAL PENGUKURAN');

        // Sub-kolom tanggal pengukuran (X-AI) di baris 3-5 - 12 bulan
        $bulanKosong = ['JAN', 'FEB', 'MAR', 'APR', 'MEI', 'JUN', 'JUL', 'AGS', 'SEPT', 'OKT', 'NOV', 'DES'];
        $currentCol = 'X';
        
        for ($i = 0; $i < 12; $i++) {
            $sheet->mergeCells($currentCol.'3:'.$currentCol.'5');
            $sheet->setCellValue($currentCol.'3', $bulanKosong[$i]);
            $currentCol = $this->getColumnLetter($currentCol, 1);
        }

        // BLN PERTAMA TIMBANG di AJ
        $sheet->mergeCells('AJ2:AJ5');
        $sheet->setCellValue('AJ2', 'BLN PERTAMA TIMBANG');
        
        // IMD di AK
        $sheet->mergeCells('AK2:AK5');
        $sheet->setCellValue('AK2', 'IMD');

        // UMUR BULAN JALAN di AL
        $sheet->mergeCells('AL2:AL5');
        $sheet->setCellValue('AL2', 'UMUR BULAN JALAN');

        // Setup bulan-bulan sebagai HEADER UTAMA di baris 2 (JANUARI - DESEMBER)
        $months = ['JANUARI', 'FEBRUARI', 'MARET', 'APRIL', 'MEI', 'JUNI', 
                  'JULI', 'AGUSTUS', 'SEPTEMBER', 'OKTOBER', 'NOVEMBER', 'DESEMBER'];
        
        $currentCol = 'AM'; // Mulai dari AM setelah UMUR BULAN JALAN di AL
        
        for ($month = 0; $month < 12; $month++) {
            // Header bulan di baris 2 (8 kolom per bulan - menambah 1 kolom untuk Umur)
            $startCol = $currentCol;
            $endCol = $this->getColumnLetter($currentCol, 7); // 7 offset karena 8 kolom
            
            $sheet->mergeCells($startCol . '2:' . $endCol . '2');
            $sheet->setCellValue($startCol . '2', $months[$month]);
            
            // Sub header di baris 3-5 (8 kolom per bulan)
            for ($i = 0; $i < 8; $i++) {
                $col = $this->getColumnLetter($currentCol, $i);
                
                if ($i == 0) { // Umur (Bln)
                    $sheet->mergeCells($col.'3:'.$col.'5');
                    $sheet->setCellValue($col.'3', 'Umur (BLN)');
                } elseif ($i == 1) { // BB
                    $sheet->mergeCells($col.'3:'.$col.'5');
                    $sheet->setCellValue($col.'3', 'BB (KG)');
                } elseif ($i == 2) { // TB
                    $sheet->mergeCells($col.'3:'.$col.'5');
                    $sheet->setCellValue($col.'3', 'TB (CM)');
                } elseif ($i == 3) { // ASI-E Ya
                    $nextCol = $this->getColumnLetter($currentCol, 4);
                    $sheet->mergeCells($col.'3:'.$nextCol.'3');
                    $sheet->setCellValue($col.'3', 'ASI-E');
                    
                    $sheet->mergeCells($col.'4:'.$col.'5');
                    $sheet->setCellValue($col.'4', 'Ya');
                } elseif ($i == 4) { // ASI-E Tdk
                    $sheet->mergeCells($col.'4:'.$col.'5');
                    $sheet->setCellValue($col.'4', 'Tdk');
                } elseif ($i == 5) { // LILA
                    $sheet->mergeCells($col.'3:'.$col.'5');
                    $sheet->setCellValue($col.'3', 'LILA (CM)');
                } elseif ($i == 6) { // LK
                    $sheet->mergeCells($col.'3:'.$col.'5');
                    $sheet->setCellValue($col.'3', 'LK (CM)');
                } elseif ($i == 7) { // MP-ASI
                    $sheet->mergeCells($col.'3:'.$col.'5');
                    $sheet->setCellValue($col.'3', 'MP-ASI');
                }
            }
            
            $currentCol = $this->getColumnLetter($currentCol, 8); // 8 kolom per bulan
        }
    }

    // Method untuk mendapatkan nama kolom Excel (A, B, C, ..., AA, AB, dst)
    private function getColumnLetter($startCol, $offset)
    {
        $startNum = 0;
        for ($i = 0; $i < strlen($startCol); $i++) {
            $startNum = $startNum * 26 + (ord($startCol[$i]) - ord('A') + 1);
        }
        
        $targetNum = $startNum + $offset;
        
        $result = '';
        while ($targetNum > 0) {
            $targetNum--;
            $result = chr(ord('A') + ($targetNum % 26)) . $result;
            $targetNum = intval($targetNum / 26);
        }
        
        return $result;
    }

    // Method untuk mengatur lebar kolom
    private function setColumnWidths($sheet)
    {
        // Width untuk kolom identitas
        $widths = [
            'A' => 15, 'B' => 6, 'C' => 6, 'D' => 6, 'E' => 5,
            'F' => 6, 'G' => 7, 'H' => 6, 'I' => 5, 'J' => 20,
            'K' => 4, 'L' => 4, 'M' => 12, 'N' => 4, 'O' => 20,
            'P' => 4, 'Q' => 4, 'R' => 4, 'S' => 5
        ];

        foreach ($widths as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        // Set width untuk kolom THN SEBELUMNYA (T-W) - 4 kolom
        $currentCol = 'T';
        for ($i = 0; $i < 4; $i++) {
            $sheet->getColumnDimension($currentCol)->setWidth(4);
            $currentCol = $this->getColumnLetter($currentCol, 1);
        }

        // Set width untuk kolom TANGGAL PENGUKURAN (X-AI) - 12 kolom
        $currentCol = 'X';
        for ($i = 0; $i < 12; $i++) {
            $sheet->getColumnDimension($currentCol)->setWidth(4);
            $currentCol = $this->getColumnLetter($currentCol, 1);
        }

        // Set width untuk kolom AJ (BLN PERTAMA TIMBANG), AK (IMD), AL (UMUR BULAN JALAN) - 3 kolom
        $fixedCols = ['AJ' => 8, 'AK' => 4, 'AL' => 8];
        foreach ($fixedCols as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        // Set width yang SAMA untuk semua kolom pengukuran bulanan (AM-ED)
        // 12 bulan x 8 kolom = 96 kolom total dari AM sampai ED
        $currentCol = 'AM';
        for ($i = 0; $i < 96; $i++) {
            // Semua kolom pengukuran bulanan dibuat sama lebar
            $sheet->getColumnDimension($currentCol)->setWidth(4.5);
            $currentCol = $this->getColumnLetter($currentCol, 1);
        }
    }

    public function collection()
    {
        $balitas = DataBalita::orderBy('nama')->get();
        
        $data = [];
        
        foreach ($balitas as $balita) {
            $row = [
                $balita->nik ?? '',
                $balita->prov ?? '',
                $balita->kab ?? '',
                $balita->kec ?? '',
                $balita->anak_ke ?? '',
                $balita->bb_lahir ?? '',
                $balita->panjang_lahir ?? '',
                $balita->nik_2_dig ?? '',
                $balita->buku_kia ?? '',
                $balita->nama_ortu ?? '',
                $balita->rt ?? '',
                $balita->rw ?? '',
                $balita->posyandu ?? '',
                $balita->no_reg ?? '',
                $balita->nama ?? '',
                $this->getGenderCode($balita->jenis_kelamin),
                $balita->tanggal_lahir ? Carbon::parse($balita->tanggal_lahir)->format('d') : '',
                $balita->tanggal_lahir ? Carbon::parse($balita->tanggal_lahir)->format('m') : '',
                $balita->tanggal_lahir ? Carbon::parse($balita->tanggal_lahir)->format('Y') : '',
            ];

            // THN SEBELUMNYA - 4 kolom (NOV BB, NOV TB, DES BB, DES TB)
            $prevYear = $this->year - 1;
            
            // NOV tahun sebelumnya
            $novAbsen = AbsenBalita::where('no_reg', $balita->no_reg)
                ->whereYear('tanggal_absen', $prevYear)
                ->whereMonth('tanggal_absen', 11)
                ->first();
            
            $row[] = $novAbsen ? number_format((float)$novAbsen->bb, 1) : '';
            $row[] = $novAbsen ? number_format((float)$novAbsen->tb, 1) : '';
            
            // DES tahun sebelumnya
            $desAbsen = AbsenBalita::where('no_reg', $balita->no_reg)
                ->whereYear('tanggal_absen', $prevYear)
                ->whereMonth('tanggal_absen', 12)
                ->first();
            
            $row[] = $desAbsen ? number_format((float)$desAbsen->bb, 1) : '';
            $row[] = $desAbsen ? number_format((float)$desAbsen->tb, 1) : '';

            // TANGGAL PENGUKURAN - 12 kolom kosong (X-AI)
            for ($i = 0; $i < 12; $i++) {
                $row[] = '';
            }

            // BLN PERTAMA TIMBANG - 1 kolom
            $row[] = '';
            
            // IMD - 1 kolom
            $row[] = '';
            
            // UMUR BULAN JALAN - 1 kolom
            $umur = '';
            if ($balita->tanggal_lahir) {
                try {
                    $umur = Carbon::parse($balita->tanggal_lahir)->diffInMonths(Carbon::now());
                } catch (Exception $e) {
                    $umur = '';
                }
            }
            $row[] = $umur;

            // Pengukuran bulanan (JANUARI - DESEMBER) - 96 kolom (12 bulan x 8 kolom)
            for ($month = 1; $month <= 12; $month++) {
                $absen = AbsenBalita::where('no_reg', $balita->no_reg)
                    ->whereYear('tanggal_absen', $this->year)
                    ->whereMonth('tanggal_absen', $month)
                    ->first();

                if ($absen) {
                    // Hitung umur pada bulan tersebut
                    $umurBulan = '';
                    if ($balita->tanggal_lahir && $absen->tanggal_absen) {
                        try {
                            $lahir = Carbon::parse($balita->tanggal_lahir);
                            $tanggalAbsen = Carbon::parse($absen->tanggal_absen);
                            $umurBulan = $lahir->diffInMonths($tanggalAbsen);
                        } catch (Exception $e) {
                            $umurBulan = '';
                        }
                    }
                    
                    $row = array_merge($row, [
                        $umurBulan, // Umur (Bln)
                        $absen->bb ? number_format((float)$absen->bb, 1) : '',
                        $absen->tb ? number_format((float)$absen->tb, 1) : '',
                        '', // ASI-E Ya
                        '', // ASI-E Tdk
                        $absen->lila ? number_format((float)$absen->lila, 1) : '',
                        '', // LK
                        '' // MP-ASI
                    ]);
                } else {
                    $row = array_merge($row, ['', '', '', '', '', '', '', '']);
                }
            }

            $data[] = $row;
        }

        return collect($data);
    }

    public function startCell(): string
    {
        return 'A6';
    }

    public function headings(): array
    {
        return [];
    }

    public function styles(Worksheet $sheet)
    {
        // Total kolom: A-S(19) + T-W(4) + X-AI(12) + AJ-AL(3) + AM-ED(96) = 134 kolom, berakhir di ED
        $lastCol = 'ED';
        
        // Judul utama
        $sheet->mergeCells('A1:' . $lastCol . '1');
        $sheet->setCellValue('A1', 'HASIL PENGUKURAN ANTROPOMETRI BALITA');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);

        // Style untuk header
        $sheet->getStyle('A2:' . $lastCol . '5')->applyFromArray([
            'font' => ['size' => 8, 'bold' => true],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);

        // Row heights
        $sheet->getRowDimension(1)->setRowHeight(25);
        for ($i = 2; $i <= 5; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(20);
        }

        $this->setColumnWidths($sheet);
        
        return $sheet;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                $this->setupHeaders($sheet);
                
                $lastRow = 5 + $this->collection()->count();
                $lastCol = 'ED';
                
                $sheet->getStyle('A6:' . $lastCol . $lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000']
                        ]
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER
                    ],
                    'font' => ['size' => 8]
                ]);
            }
        ];
    }

    private function getGenderCode($jenisKelamin)
    {
        if (!$jenisKelamin) return '';
        
        $gender = strtoupper(trim($jenisKelamin));
        
        if ($gender === 'LAKI-LAKI' || $gender === 'L') {
            return '1';
        } elseif ($gender === 'PEREMPUAN' || $gender === 'P') {
            return '2';
        }
        
        return '';
    }

    private function hitungUmur($tanggalLahir, $tanggalAbsen)
    {
        if (!$tanggalLahir || !$tanggalAbsen) return '';
        
        try {
            $lahir = Carbon::parse($tanggalLahir);
            $absen = Carbon::parse($tanggalAbsen);
            return $lahir->diffInMonths($absen);
        } catch (Exception $e) {
            return '';
        }
    }
}
