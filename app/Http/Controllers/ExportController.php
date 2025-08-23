<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\AbsenBalitaExport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\TanggalAktif;
use Carbon\Carbon;

class ExportController extends Controller
{
    public function exportAbsenBalita()
    {
        $tanggalAktif = TanggalAktif::first()->tanggal;
        $tanggal = Carbon::parse($tanggalAktif)->format('d-m-Y');
        $filename = 'absen-balita-' . $tanggal . '.xlsx';
        return Excel::download(new AbsenBalitaExport, $filename);
    }
}
