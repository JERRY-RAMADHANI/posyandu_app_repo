<?php

namespace App\Http\Controllers;

use App\Models\DataBalita;
use Illuminate\Http\Request;

class DaftarBalitaController extends Controller
{
    public function index(Request $request)
    {
        $query = DataBalita::query();

        // Search jika ada keyword
        if ($request->has('search') && $request->search != '') {
            $keyword = $request->search;
            $query->where(function ($q) use ($keyword) {
                $q->where('nama', 'like', "%{$keyword}%")
                    ->orWhere('nik', 'like', "%{$keyword}%")
                    ->orWhere('no_reg', 'like', "%{$keyword}%");
            });
        }

        // Urutkan dari terbaru
        $dataBalita = $query->orderBy('id', 'desc')->paginate(10);

        // Biar search tetap kebawa di pagination
        $dataBalita->appends(['search' => $request->search]);

        return view('main.admin.EditBalita', compact('dataBalita'));
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


    public function create()
    {
        $lastId = DataBalita::max('id') ?? 0;
        $nextId = $lastId + 1;

        $no_reg = 'JRB' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        return view('main.admin.daftarBalita', compact('no_reg'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'no_reg' => 'required|string',
            'nik' => 'nullable|string',
            'nama' => 'nullable|string',
            'tanggal_lahir' => 'nullable|string',
            'usia' => 'nullable|integer',
            'jenis_kelamin' => 'nullable|string',
            'alamat' => 'nullable|string',
            'rt' => 'nullable|string|max:3',
            'rw' => 'nullable|string|max:3',
            'nama_ortu' => 'nullable|string',
            'panjang_lahir' => 'nullable|numeric',
            'bb_lahir' => 'nullable|numeric',
            'anak_ke' => 'nullable|integer',
            'buku_kia' => 'nullable|integer',
        ]);

        // Process NIK and date fields like before
        if (!empty($validated['nik'])) {
            $validated['prov'] = substr($validated['nik'], 0, 2);
            $validated['kab'] = substr($validated['nik'], 2, 2);
            $validated['kec'] = substr($validated['nik'], 4, 2);
            $validated['nik_2_dig'] = substr($validated['nik'], -2);
        }

        if (!empty($validated['tanggal_lahir'])) {
            $date = \Carbon\Carbon::parse($validated['tanggal_lahir']);
            $validated['thn'] = $date->year;
            $validated['bln'] = $date->month;
            $validated['tgl'] = $date->day;
        }

        $validated['jenis_kelamin'] = strtoupper($validated['jenis_kelamin']);
        $validated['posyandu'] = 'JERUK';

        // Create data balita
        $dataBalita = DataBalita::create($validated);

        // Get active date from tanggal_aktifs
        $tanggalAktif = \App\Models\TanggalAktif::first()->tanggal;

        // Create absen entry for the same day
        \App\Models\AbsenBalita::create([
            'no_reg' => $validated['no_reg'],
            'nik' => $validated['nik'],
            'nama' => $validated['nama'],
            'tanggal_lahir' => $validated['tanggal_lahir'],
            'usia' => $validated['usia'],
            'alamat' => $validated['alamat'],
            'tanggal_absen' => $tanggalAktif,
            'bb' => null,
            'tb' => null,
            'lk' => null,
            'll' => null,
            'ket' => null,
        ]);

        return redirect()->route('daftar.balita')
            ->with('success', 'Data berhasil ditambahkan dan auto absen untuk hari ini');
    }

    public function edit(DataBalita $dataBalita)
    {
        return view('main.admin.UpdateBalita', compact('dataBalita'));
    }

    public function update(Request $request, DataBalita $dataBalita)
    {
        $validated = $request->validate([
            'nik' => 'nullable|string',
            'nama' => 'nullable|string',
            'tanggal_lahir' => 'nullable|string',
            'usia' => 'nullable|integer',
            'jenis_kelamin' => 'nullable|string',
            'alamat' => 'nullable|string',
            'rt' => 'nullable|string|max:3',
            'rw' => 'nullable|string|max:3',
            'panjang_lahir' => 'nullable|numeric',
            'nama_ortu' => 'nullable|string',
            'bb_lahir' => 'nullable|numeric',
            'anak_ke' => 'nullable|integer',
            'buku_kia' => 'nullable|integer',
        ]);

        if (!empty($validated['nik'])) {
            // Perbaiki nama field sesuai dengan migration dan model
            $validated['prov'] = substr($validated['nik'], 0, 2);
            $validated['kab'] = substr($validated['nik'], 2, 2);
            $validated['kec'] = substr($validated['nik'], 4, 2);
            $validated['nik_2_dig'] = substr($validated['nik'], -2);
        }

        if (!empty($validated['tanggal_lahir'])) {
            $date = \Carbon\Carbon::parse($validated['tanggal_lahir']);
            $validated['thn'] = $date->year;
            $validated['bln'] = $date->month;
            $validated['tgl'] = $date->day;
        }

        // Ensure status and jenis_kelamin are uppercase
        if (isset($validated['jenis_kelamin'])) {
            $validated['jenis_kelamin'] = strtoupper($validated['jenis_kelamin']);
        }

        $validated['posyandu'] = 'JERUK';

        $dataBalita->update($validated);

        return redirect()->route('edit.balita')
            ->with('success', 'Data berhasil diperbarui');
    }
}
