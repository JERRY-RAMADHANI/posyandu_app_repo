<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\DataBalita;
use App\Models\AbsenBalita;
use App\Models\TanggalAktif;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AbsenBalitaController extends Controller
{
    public function index()
    {
        // If no tanggal_absen in session, set today's date
        if (!session()->has('tanggal')) {
            session(['tanggal_absen' => Carbon::now()->toDateString()]);
        }

        return view('main.admin.absenBalitaAbsenBalita');
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
            'no_reg' => 'required|string',
            'nik' => 'required|string',
            'nama' => 'required|string',
            'tanggal_lahir' => 'required|date',
            'usia' => 'required|integer',
            'alamat' => 'required|string'
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

        // Add tanggal_absen to validated data
        $validated['tanggal_absen'] = $tanggal_absen;

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
            'bmi' => null,
            'hasil' => null,
            'ket' => null,
            'note' => null
        ]);

        AbsenBalita::create($validated);

        return redirect()->back()->with('success', 'Data absensi berhasil disimpan');
    }

    public function edit($id)
    {
        $absenBalitaAbsenBalita = AbsenBalita::where('id', $id)
            ->whereDate('tanggal_absen', session('tanggal'))
            ->firstOrFail();

        return view('main.admin.UpdateAbsenBalita', compact('absenBalitaAbsenBalita'));
    }

    public function update(Request $request, $id)
    {
        $absenBalitaAbsenBalita = AbsenBalita::where('id', $id)
            ->whereDate('tanggal_absen', session('tanggal'))
            ->firstOrFail();

        $validated = $request->validate([
            'no_reg' => 'required|string',
            'nik' => 'required|string',
            'nama' => 'required|string',
            'tanggal_lahir' => 'required|date',
            'usia' => 'required|integer',
            'alamat' => 'required|string',
            'bb' => 'nullable|integer',
            'tb' => 'nullable|integer',
            'lp' => 'nullable|integer',
            'lila' => 'nullable|integer',
            'sistole' => 'nullable|integer',
            'diastole' => 'nullable|integer',
            'au' => 'nullable|integer',
            'gda' => 'nullable|integer',
            'kol' => 'nullable|integer',
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

        $absenBalitaAbsenBalita->update($validated);

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

        $absenBalitaAbsenBalita = AbsenBalita::where('id', $id)
            ->whereDate('tanggal_absen', $tanggalAktif)
            ->firstOrFail();

        $validated = $request->validate([
            'bb' => 'nullable|integer',
            'tb' => 'nullable|integer',
            'lp' => 'nullable|integer',
            'lila' => 'nullable|integer',
            'sistole' => 'nullable|integer',
            'diastole' => 'nullable|integer',
            'ket' => 'nullable|string',
        ]);

        // Calculate BMI if weight and height are provided
        // if ($request->bb && $request->tb) {
        //     $height_m = $request->tb / 100;
        //     $bmi = round($request->bb / ($height_m * $height_m), 2);
        //     $validated['bmi'] = $bmi;
        //     $validated['hasil'] = $this->getBmiStatus($bmi);
        // }

        $absenBalitaAbsenBalita->update($validated);

        return redirect()->route('formDewasa')
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

        $absenKosong = AbsenBalita::whereDate('tanggal_absen', $tanggalAktif)
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

        $absenBalitaAbsenBalita = AbsenBalita::where('id', $id)
            ->whereDate('tanggal_absen', $tanggalAktif)
            ->firstOrFail();

        $validated = $request->validate([
            'au' => 'nullable|numeric',
            'gda' => 'nullable|numeric',
            'kol' => 'nullable|numeric',
            'ket' => 'nullable|string',
        ]);

        $absenBalitaAbsenBalita->update($validated);

        return redirect()->route('formDarah')
            ->with('success', 'Data berhasil diperbarui');
    }
}
