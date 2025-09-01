<?php

namespace App\Http\Controllers;

use App\Models\DataBalita;
use App\Models\AbsenBalita;
use App\Models\TanggalAktif;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Routing\Controller;
use Exception; 

class AbsensiBalitaController extends Controller
{
    public function index()
    {
        // If no tanggal_absen in session, set today's date
        if (!session()->has('tanggal')) {
            session(['tanggal_absen' => Carbon::now()->toDateString()]);
        }

        return view('main.admin.absenBalita');
    }

    public function index2(Request $request)
    {
        $query = AbsenBalita::whereDate('tanggal_absen', session('tanggal'));

        if ($request->filled('search')) {
            $keyword = $request->search;
            $query->where(function ($q) use ($keyword) {
                $q->where('nama', 'like', "%{$keyword}%")
                    ->orWhere('nik', 'like', "%{$keyword}%")
                    ->orWhere('no_reg', 'like', "%{$keyword}%");
            });
        }

        $dataAbsenBalita = $query->orderBy('id', 'desc')->paginate(10);
        $dataAbsenBalita->appends(['search' => $request->search]);

        return view('main.admin.EditAbsenBalita', compact('dataAbsenBalita'));
    }

    public function search(Request $request)
    {
        $search = $request->search;
        $type = $request->type;

        $query = DataBalita::query();

        if ($type === 'reg') {
            $query->where('no_reg', 'LIKE', "{$search}%");
        } else {
            $query->where('nama', 'LIKE', "%{$search}%");
        }

        return $query->limit(10)->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'no_reg' => 'nullable|string',
            'nik' => 'nullable|string',
            'nama' => 'nullable|string',
            'tanggal_lahir' => 'nullable|date',
            'usia' => 'nullable|integer',
            'alamat' => 'nullable|string'
        ]);

        // Get tanggal_absen from session
        $tanggal_absen = session('tanggal');

        // Check if person already attended today
        $existingAbsen = AbsenBalita::where('no_reg', $validated['no_reg'])
            ->whereDate('tanggal_absen', $tanggal_absen)
            ->first();

        if ($existingAbsen) {
            return redirect()->back()
                ->with('error', $validated['nama'] . ' sudah melakukan absensi pada tanggal ' . date('d-m-Y', strtotime($tanggal_absen)));
        }

        // Hitung ulang umur berdasarkan tanggal aktif
        $umurBaru = $validated['usia'];
        if ($validated['tanggal_lahir']) {
            $tanggalAktif = TanggalAktif::first()->tanggal;
            $umurBaru = $this->hitungUmurBalita($validated['tanggal_lahir'], $tanggalAktif);
        }

        // Update umur di tabel data_balitas
        if ($validated['no_reg']) {
            DataBalita::where('no_reg', $validated['no_reg'])
                ->update(['usia' => $umurBaru]);
        }

        // Add tanggal_absen to validated data
        $validated['tanggal_absen'] = $tanggal_absen;
        $validated['usia'] = $umurBaru; // Gunakan umur yang sudah dihitung ulang

        // Set other fields to null initially
        $validated = array_merge($validated, [
            'bb' => null,
            'tb' => null,
            'lk' => null,
            'll' => null,
            'ket' => null,
        ]);

        AbsenBalita::create($validated);

        return redirect()->back()->with('success', 'Data absensi berhasil disimpan');
    }

    public function edit($id)
    {
        $absenBalita = AbsenBalita::where('id', $id)
            ->whereDate('tanggal_absen', session('tanggal'))
            ->firstOrFail();

        return view('main.admin.UpdateAbsenBalita', compact('absenBalita'));
    }

    public function update(Request $request, $id)
    {
        $absenBalita = AbsenBalita::where('id', $id)
            ->whereDate('tanggal_absen', session('tanggal'))
            ->firstOrFail();

        $validated = $request->validate([
            'no_reg' => 'nullable|string',
            'nik' => 'nullable|string',
            'nama' => 'nullable|string',
            'tanggal_lahir' => 'nullable|date',
            'usia' => 'nullable|integer',
            'alamat' => 'nullable|string',
            'bb' => 'nullable|numeric',
            'tb' => 'nullable|numeric',
            'lk' => 'nullable|numeric',
            'll' => 'nullable|numeric',
            'ket' => 'nullable|string',
        ]);

        // Calculate BMI if weight and height are provided
        // if ($request->bb && $request->tb) {
        //     $height_m = $request->tb / 100;
        //     $bmi = round($request->bb / ($height_m * $height_m), 2);
        //     $validated['bmi'] = $bmi;
        //     $validated['hasil'] = $this->getBmiStatus($bmi);
        // }

        $absenBalita->update($validated);

        return redirect()->route('edit.absen.balita')
            ->with('success', 'Data berhasil diperbarui');
    }

    // private function getBmiStatus($bmi)
    // {
    //     if ($bmi < 18.5) return 'KURUS';
    //     if ($bmi < 25) return 'NORMAL';
    //     if ($bmi < 30) return 'GEMUK';
    //     return 'OBESITAS';
    // }

    public function isiBB(Request $request, $id)
    {
        $tanggalAktif = \App\Models\TanggalAktif::first()->tanggal;

        $absenBalita = AbsenBalita::where('id', $id)
            ->whereDate('tanggal_absen', $tanggalAktif)
            ->firstOrFail();

        $validated = $request->validate([
            'bb' => 'nullable|numeric',
            'tb' => 'nullable|numeric',
            'lk' => 'nullable|numeric',
            'll' => 'nullable|numeric',
        ]);

        // Calculate BMI if weight and height are provided
        // if ($request->bb && $request->tb) {
        //     $height_m = $request->tb / 100;
        //     $bmi = round($request->bb / ($height_m * $height_m), 2);
        //     $validated['bmi'] = $bmi;
        //     $validated['hasil'] = $this->getBmiStatus($bmi);
        // }

        $absenBalita->update($validated);

        return redirect()->route('formAnak')
            ->with('success', 'Data berhasil diperbarui');
    }

    public function indexIsiBB()
    {
        // Get active date from tanggal_aktif table
        $tanggalAktif = \App\Models\TanggalAktif::first()->tanggal;

        // Get absen records with empty measurements for active date
        $absenKosong = AbsenBalita::whereDate('tanggal_absen', $tanggalAktif)
            ->where(function ($query) {
                $query->whereNull('bb')
                    ->orWhereNull('tb')
                    ->orWhereNull('lk')
                    ->orWhereNull('ll');
            })
            ->get();

        return view('main.formAnak', compact('absenKosong', 'tanggalAktif'));
    }

    public function indexIsiKet()
    {

        $tanggalAktif = \App\Models\TanggalAktif::first()->tanggal;

        $absenKosong = AbsenBalita::whereDate('tanggal_absen', $tanggalAktif)
            ->where(function ($query) {
                $query->whereNull('ket');
            })
            ->get();

        return view('main.formNote', compact('absenKosong', 'tanggalAktif'));
    }

    public function isiKet(Request $request, $id)
    {
        $tanggalAktif = \App\Models\TanggalAktif::first()->tanggal;

        $absenBalita = AbsenBalita::where('id', $id)
            ->whereDate('tanggal_absen', $tanggalAktif)
            ->firstOrFail();

        $validated = $request->validate([
            'ket' => 'nullable|string',
        ]);

        $absenBalita->update($validated);

        return redirect()->route('formNote')
            ->with('success', 'Data berhasil diperbarui');
    }

    // Tambahkan method baru untuk hitung umur balita (dalam bulan)
    private function hitungUmurBalita($tanggalLahir, $tanggalAktif)
    {
        try {
            $lahir = \Carbon\Carbon::parse($tanggalLahir);
            $aktif = \Carbon\Carbon::parse($tanggalAktif);
            
            // Calculate total months
            $months = ($aktif->year - $lahir->year) * 12;
            $months += $aktif->month - $lahir->month;

            // Adjust for day of month
            if ($aktif->day < $lahir->day) {
                $months--;
            }

            // Ensure we never return negative months
            return max(0, $months);
        } catch (Exception $e) {
            return 0;
        }
    }
}
