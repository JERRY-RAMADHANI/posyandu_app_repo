<?php

namespace App\Exports;

use App\Models\DataBalita;
use App\Models\AbsenBalita;
use App\Models\TanggalAktif;
use Carbon\Carbon;
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

    public function collection()
    {
        $balitas = DataBalita::where('status', 'aktif')->orderBy('nama')->get();
        
        $data = [];
        $no = 1;
        
        foreach ($balitas as $balita) {
            $row = [
                $no++, // No
                $balita->nama, // Nama
                $balita->tanggal_lahir ? Carbon::parse($balita->tanggal_lahir)->format('d/m/Y') : '',
                $balita->jenis_kelamin == 'L' ? 'L' : 'P',
                "RT {$balita->rt} RW {$balita->rw}",
                $balita->nama_ayah,
                $balita->nama_ibu
            ];

            // Data untuk setiap bulan (Januari - Desember)
            for ($month = 1; $month <= 12; $month++) {
                $absen = AbsenBalita::where('no_reg', $balita->no_reg)
                    ->whereYear('tanggal_absen', $this->year)
                    ->whereMonth('tanggal_absen', $month)
                    ->first();

                if ($absen) {
                    $umur = $this->hitungUmur($balita->tanggal_lahir, $absen->tanggal_absen);
                    
                    $row = array_merge($row, [
                        $umur['bulan'], // Umur bulan
                        number_format($absen->bb, 1), // BB
                        number_format($absen->tb, 1), // TB
                        $this->getStatusGizi($absen->bb, $absen->tb, $balita->jenis_kelamin, $umur['bulan']), // Status
                        $absen->lila ? number_format($absen->lila, 1) : '', // LILA
                        $this->getKeterangan($absen) // Keterangan
                    ]);
                } else {
                    // Jika tidak ada data absen untuk bulan ini
                    $row = array_merge($row, ['', '', '', '', '', '']);
                }
            }

            $data[] = $row;
        }

        return collect($data);
    }

    public function startCell(): string
    {
        return 'A4';
    }

    public function headings(): array
    {
        $headers = [
            'No',
            'NAMA BALITA',
            'TGL LAHIR',
            'L/P',
            'ALAMAT',
            'NAMA AYAH',
            'NAMA IBU'
        ];

        // Header bulan dari Januari sampai Desember
        $bulan = [
            'JANUARI', 'FEBRUARI', 'MARET', 'APRIL', 'MEI', 'JUNI',
            'JULI', 'AGUSTUS', 'SEPTEMBER', 'OKTOBER', 'NOVEMBER', 'DESEMBER'
        ];

        foreach ($bulan as $namaBulan) {
            $headers = array_merge($headers, [
                "UMUR\n$namaBulan", 
                'BB', 
                'TB', 
                'STATUS', 
                'LILA', 
                'KET'
            ]);
        }

        return $headers;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Set row heights
                $sheet->getRowDimension(1)->setRowHeight(25);
                $sheet->getRowDimension(2)->setRowHeight(20);
                $sheet->getRowDimension(3)->setRowHeight(20);
                $sheet->getRowDimension(4)->setRowHeight(40);

                // Merge cells untuk header utama
                $this->mergeMonthHeaders($sheet);
                
                // Set column widths
                $this->setColumnWidths($sheet);
            }
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Title
        $sheet->mergeCells('A1:BF1');
        $sheet->setCellValue('A1', 'HASIL PENGUKURAN ANTROPOMETRI BALITA');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        // Sub header untuk tahun
        $sheet->mergeCells('A2:BF2');
        $sheet->setCellValue('A2', 'TAHUN ' . $this->year);
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        // Header bulan
        $this->setMonthHeaders($sheet);

        // Style untuk header kolom
        $lastCol = 'BF'; // Kolom terakhir (7 + 12*6 = 79 kolom)
        $sheet->getStyle('A4:' . $lastCol . '4')->applyFromArray([
            'font' => ['bold' => true, 'size' => 10],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E2E2E2']
            ],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true
            ]
        ]);

        return $sheet;
    }

    private function setMonthHeaders($sheet)
    {
        $bulan = [
            'JANUARI', 'FEBRUARI', 'MARET', 'APRIL', 'MEI', 'JUNI',
            'JULI', 'AGUSTUS', 'SEPTEMBER', 'OKTOBER', 'NOVEMBER', 'DESEMBER'
        ];

        $startCol = 8; // Mulai dari kolom H (setelah data identitas)
        
        foreach ($bulan as $index => $namaBulan) {
            $colStart = $this->getColumnLetter($startCol + ($index * 6));
            $colEnd = $this->getColumnLetter($startCol + ($index * 6) + 5);
            
            $sheet->mergeCells($colStart . '3:' . $colEnd . '3');
            $sheet->setCellValue($colStart . '3', $namaBulan);
            $sheet->getStyle($colStart . '3:' . $colEnd . '3')->applyFromArray([
                'font' => ['bold' => true, 'size' => 11],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'D9D9D9']
                ],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ]);
        }
    }

    private function setColumnWidths($sheet)
    {
        // Kolom identitas
        $sheet->getColumnDimension('A')->setWidth(5);  // No
        $sheet->getColumnDimension('B')->setWidth(20); // Nama
        $sheet->getColumnDimension('C')->setWidth(12); // TGL Lahir
        $sheet->getColumnDimension('D')->setWidth(5);  // L/P
        $sheet->getColumnDimension('E')->setWidth(15); // Alamat
        $sheet->getColumnDimension('F')->setWidth(20); // Nama Ayah
        $sheet->getColumnDimension('G')->setWidth(20); // Nama Ibu

        // Kolom data bulanan (6 kolom per bulan x 12 bulan)
        for ($i = 8; $i <= 79; $i++) {
            $col = $this->getColumnLetter($i);
            $width = ($i - 8) % 6 == 0 ? 8 : 6; // Kolom umur lebih lebar
            $sheet->getColumnDimension($col)->setWidth($width);
        }
    }

    private function getColumnLetter($index)
    {
        $letters = '';
        while ($index > 0) {
            $index--;
            $letters = chr(65 + ($index % 26)) . $letters;
            $index = intval($index / 26);
        }
        return $letters;
    }

    private function hitungUmur($tanggalLahir, $tanggalAbsen)
    {
        $lahir = Carbon::parse($tanggalLahir);
        $absen = Carbon::parse($tanggalAbsen);
        
        $bulan = $lahir->diffInMonths($absen);
        
        return [
            'bulan' => $bulan,
            'tahun' => intval($bulan / 12)
        ];
    }

    private function getStatusGizi($bb, $tb, $jenisKelamin, $umurBulan)
    {
        // Implementasi sederhana status gizi
        // Bisa disesuaikan dengan standar WHO/Kemenkes
        if (!$bb || !$tb) return '';
        
        $bmi = $bb / (($tb/100) * ($tb/100));
        
        if ($bmi < 17) return 'Kurus';
        if ($bmi > 25) return 'Gemuk';
        return 'Normal';
    }

    private function getKeterangan($absen)
    {
        $ket = [];
        if ($absen->vitamin_a) $ket[] = 'Vit A';
        if ($absen->imunisasi) $ket[] = 'Imunisasi';
        
        return implode(', ', $ket);
    }
}
