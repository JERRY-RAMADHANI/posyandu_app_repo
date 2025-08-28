<?php

namespace App\Http\Controllers;

use App\Models\DataDewasa;
use App\Models\AbsenDewasa;
use App\Models\TanggalAktif;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Routing\Controller;
use Exception;

class AbsenDewasaController extends Controller
{
    public function index()
    {
        // If no tanggal_absen in session, set today's date
        if (!session()->has('tanggal')) {
            session(['tanggal_absen' => Carbon::now()->toDateString()]);
        }

        return view('main.admin.absenDewasa');
    }

    public function index2(Request $request)
    {
        $query = AbsenDewasa::whereDate('tanggal_absen', session('tanggal'));

        if ($request->filled('search')) {
            $keyword = $request->search;
            $query->where(function ($q) use ($keyword) {
                $q->where('nama', 'like', "%{$keyword}%")
                    ->orWhere('nik', 'like', "%{$keyword}%")
                    ->orWhere('no_reg', 'like', "%{$keyword}%");
            });
        }

        $dataAbsenDewasa = $query->orderBy('id', 'desc')->paginate(10);
        $dataAbsenDewasa->appends(['search' => $request->search]);

        return view('main.admin.EditAbsenDewasa', compact('dataAbsenDewasa'));
    }

    public function search(Request $request)
    {
        $search = $request->search;
        $type = $request->type;

        $query = DataDewasa::query();

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

        // Get tanggal aktif
        $tanggalAktif = TanggalAktif::first()->tanggal;

        // Check if person already attended today
        $existingAbsen = AbsenDewasa::where('no_reg', $validated['no_reg'])
            ->whereDate('tanggal_absen', $tanggalAktif)
            ->first();

        if ($existingAbsen) {
            return redirect()->back()
                ->with('error', $validated['nama'] . ' sudah melakukan absensi pada tanggal ' . date('d-m-Y', strtotime($tanggalAktif)));
        }

        // Hitung ulang umur berdasarkan tanggal aktif
        $umurBaru = $validated['usia'];
        if ($validated['tanggal_lahir']) {
            $umurBaru = $this->hitungUmurDewasa($validated['tanggal_lahir'], $tanggalAktif);
        }

        // Update umur di tabel data_dewasas
        if ($validated['no_reg']) {
            DataDewasa::where('no_reg', $validated['no_reg'])
                ->update(['umur' => $umurBaru]);
        }

        // Add tanggal_absen to validated data
        $validated['tanggal_absen'] = $tanggalAktif;
        $validated['usia'] = $umurBaru; // Gunakan umur yang sudah dihitung ulang

        // Set other fields to null initially
        $validated = array_merge($validated, [
            'bb' => null,
            'tb' => null,
            'lp' => null,
            'lila' => null,
            'sistole' => null,
            'diastole' => null,
            'au' => null,
            'gda' => null,
            'kol' => null,
            'ket' => null,
            'bmi' => null,
            'hasil' => null,
            'status' => null,
            'note' => null,
        ]);

        AbsenDewasa::create($validated);

        return redirect()->back()->with('success', 'Data absensi berhasil disimpan');
    }

    public function edit($id)
    {
        $absenDewasa = AbsenDewasa::where('id', $id)
            ->whereDate('tanggal_absen', session('tanggal'))
            ->firstOrFail();

        return view('main.admin.UpdateAbsenDewasa', compact('absenDewasa'));
    }

    public function update(Request $request, $id)
    {
        $absenDewasa = AbsenDewasa::where('id', $id)
            ->whereDate('tanggal_absen', session('tanggal'))
            ->firstOrFail();

        $validated = $request->validate([
            'no_reg' => 'required|string',
            'nik' => 'required|string',
            'nama' => 'required|string',
            'tanggal_lahir' => 'required|date',
            'usia' => 'required|integer',
            'alamat' => 'required|string',
            'bb' => 'nullable|numeric',
            'tb' => 'nullable|numeric',
            'lp' => 'nullable|numeric',
            'lila' => 'nullable|numeric',
            'sistole' => 'nullable|numeric',
            'diastole' => 'nullable|numeric',
            'au' => 'nullable|numeric',
            'gda' => 'nullable|numeric',
            'kol' => 'nullable|numeric',
            'ket' => 'nullable|string',
            'note' => 'nullable|string',
        ]);

        // Calculate BMI if weight and height are provided
        // if ($request->bb && $request->tb) {
        //     $height_m = $request->tb / 100;
        //     $bmi = round($request->bb / ($height_m * $height_m), 2);
        //     $validated['bmi'] = $bmi;
        //     $validated['hasil'] = $this->getBmiStatus($bmi);
        // }

        if ($request->bb && $request->tb) {
        [$bmi, $status] = $this->calculateBmiAndStatus($request->bb, $request->tb);
        $validated['bmi'] = $bmi;
        $validated['hasil'] = $status;

        // ✅ Update ke DataDewasa juga
        DataDewasa::where('no_reg', $absenDewasa->no_reg)->update([
            'bmi' => $status,
        ]);
    }

        $absenDewasa->update($validated);

        return redirect()->route('edit.absen.dewasa')
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

        $absenDewasa = AbsenDewasa::where('id', $id)
            ->whereDate('tanggal_absen', $tanggalAktif)
            ->firstOrFail();

        $validated = $request->validate([
            'bb' => 'nullable|numeric',
            'tb' => 'nullable|numeric',
            'lp' => 'nullable|numeric',
            'lila' => 'nullable|numeric',
            'sistole' => 'nullable|numeric',
            'diastole' => 'nullable|numeric',
        ]);

        if ($request->bb && $request->tb) {
        [$bmi, $status] = $this->calculateBmiAndStatus($request->bb, $request->tb);
        $validated['bmi'] = $bmi;
        $validated['hasil'] = $status;

        DataDewasa::where('no_reg', $absenDewasa->no_reg)->update([
            'bmi' => $status,
        ]);
    }

        // Calculate BMI if weight and height are provided
        // if ($request->bb && $request->tb) {
        //     $height_m = $request->tb / 100;
        //     $bmi = round($request->bb / ($height_m * $height_m), 2);
        //     $validated['bmi'] = $bmi;
        //     $validated['hasil'] = $this->getBmiStatus($bmi);
        // }

        $absenDewasa->update($validated);

        return redirect()->route('formDewasa')
            ->with('success', 'Data berhasil diperbarui');
    }

    public function indexIsiBB()
    {
        // Get active date from tanggal_aktif table
        $tanggalAktif = \App\Models\TanggalAktif::first()->tanggal;

        // Get absen records with empty measurements for active date
        $absenKosong = AbsenDewasa::whereDate('tanggal_absen', $tanggalAktif)
            ->where(function ($query) {
                $query->whereNull('bb')
                    ->orWhereNull('tb')
                    ->orWhereNull('lp')
                    ->orWhereNull('lila')
                    ->orWhereNull('sistole')
                    ->orWhereNull('diastole');
            })
            ->get();

        return view('main.formDewasa', compact('absenKosong', 'tanggalAktif'));
    }

    public function indexIsiDarah()
    {

        $tanggalAktif = \App\Models\TanggalAktif::first()->tanggal;

        $absenKosong = AbsenDewasa::whereDate('tanggal_absen', $tanggalAktif)
            ->where(function ($query) {
                $query->whereNull('au')
                    ->orWhereNull('kol')
                    ->orWhereNull('gda');
            })
            ->get();

        return view('main.formDarah', compact('absenKosong', 'tanggalAktif'));
    }

    public function isiDarah(Request $request, $id)
    {
        $tanggalAktif = \App\Models\TanggalAktif::first()->tanggal;

        $absenDewasa = AbsenDewasa::where('id', $id)
            ->whereDate('tanggal_absen', $tanggalAktif)
            ->firstOrFail();

        $validated = $request->validate([
            'au' => 'nullable|numeric',
            'gda' => 'nullable|numeric',
            'kol' => 'nullable|numeric',
            'ket' => 'nullable|string',
        ]);

        $absenDewasa->update($validated);

        return redirect()->route('formDarah')
            ->with('success', 'Data berhasil diperbarui');
    }

    private function calculateBmiAndStatus($bb, $tb)
    {
        if (!$bb || !$tb) {
            return [null, null];
        }

        $height_m = $tb / 100;
        $bmi = round($bb / ($height_m * $height_m), 2);

        if ($bmi < 18.5) {
            $status = 'UNDERWEIGHT';
        } elseif ($bmi < 25) {
            $status = 'NORMAL';
        } elseif ($bmi < 30) {
            $status = 'OVERWEIGHT';
        } else {
            $status = 'OBESE';
        }

        return [$bmi, $status];
    }

    // Method untuk hitung umur dewasa (dalam tahun)
    private function hitungUmurDewasa($tanggalLahir, $tanggalAktif)
    {
        try {
            $lahir = Carbon::parse($tanggalLahir);
            $aktif = Carbon::parse($tanggalAktif);
            
            $age = $aktif->year - $lahir->year;
            
            // Adjust if birthday hasn't occurred this year
            if ($aktif->month < $lahir->month || 
                ($aktif->month == $lahir->month && $aktif->day < $lahir->day)) {
                $age--;
            }
            
            return max(0, $age);
        } catch (Exception $e) {
            return 0;
        }
    }
}
