<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AbsenDewasa;
use App\Models\AbsenBalita;

class CekAbsenController extends Controller
{
    public function index()
    {
        // Ambil tanggal dari session
        $tanggalSession = session('tanggal');

        if (!$tanggalSession) {
            return redirect()->back()->with('error', 'Tanggal belum diset di session.');
        }

        // Ambil data dari dua tabel sesuai tanggal_absen
        $absenDewasa = AbsenDewasa::whereDate('tanggal_absen', $tanggalSession)->get();
        $absenBalita = AbsenBalita::whereDate('tanggal_absen', $tanggalSession)->get();

        // Kirim ke view
        return view('main.admin.CekAbsen', [
            'tanggal' => $tanggalSession,
            'dewasa' => $absenDewasa,
            'balita' => $absenBalita
        ]);
    }
}
