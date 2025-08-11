<?php

namespace App\Http\Controllers;

use App\Models\DataDewasa;
use App\Models\AbsenDewasa;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AbsenDewasaController extends Controller
{
    public function index()
    {
        return view('main.admin.absenDewasa');
    }

    public function search(Request $request)
    {
        $search = $request->search;
        
        return DataDewasa::where('no_reg', 'LIKE', "%{$search}%")
            ->orWhere('nama', 'LIKE', "%{$search}%")
            ->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'no_reg' => 'required|string',
            'nik' => 'required|string',
            'nama' => 'required|string',
            'tanggal_lahir' => 'required|date',
            'usia' => 'required|integer',
            'alamat' => 'required|string',
            'bb' => 'required|integer',
            'tb' => 'required|integer',
            'lp' => 'required|integer',
            'lila' => 'required|integer',
            'sistole' => 'required|integer',
            'diastole' => 'required|integer',
            'au' => 'required|integer',
            'gda' => 'required|integer',
            'kol' => 'required|integer',
            'ket' => 'nullable|string',
            'note' => 'nullable|string',
        ]);

        // Add current date
        $validated['tanggal_absen'] = Carbon::now()->toDateString();

        // Calculate BMI
        $height_m = $validated['tb'] / 100;
        $bmi = round($validated['bb'] / ($height_m * $height_m), 2);
        $validated['bmi'] = $bmi;

        // Determine BMI Status
        $validated['hasil'] = $this->getBmiStatus($bmi);

        AbsenDewasa::create($validated);

        return redirect()->back()->with('success', 'Absensi berhasil disimpan');
    }

    private function getBmiStatus($bmi)
    {
        if ($bmi < 18.5) return 'Kurus';
        if ($bmi < 25) return 'Normal';
        if ($bmi < 30) return 'Gemuk';
        return 'Obesitas';
    }
}
