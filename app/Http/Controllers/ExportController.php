<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\TanggalAktif;
use Illuminate\Http\Request;
use App\Exports\AbsenBalitaExport;
use App\Exports\AbsenDewasaExport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function exportAbsenBalita()
    {
        $tanggalAktif = TanggalAktif::first()->tanggal;
        $tanggal = Carbon::parse($tanggalAktif)->format('d-m-Y');
        $filename = 'absen-balita-' . $tanggal . '.xlsx';
        return Excel::download(new AbsenBalitaExport, $filename);
    }

    public function exportAbsenDewasa()
    {
        $tanggalAktif = TanggalAktif::first()->tanggal;
        $tanggal = Carbon::parse($tanggalAktif)->format('d-m-Y');
        $filename = 'absen-dewasa-' . $tanggal . '.xlsx';
        return Excel::download(new AbsenDewasaExport, $filename);
    }
}
