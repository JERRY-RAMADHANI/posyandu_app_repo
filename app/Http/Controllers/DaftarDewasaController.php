<?php

namespace App\Http\Controllers;

use App\Models\DataDewasa;
use Illuminate\Http\Request;

class DaftarDewasaController extends Controller
{
    public function index(Request $request)
    {
        $query = DataDewasa::query();

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
        $dataDewasa = $query->orderBy('id', 'desc')->paginate(10);

        // Biar search tetap kebawa di pagination
        $dataDewasa->appends(['search' => $request->search]);

        return view('main.admin.EditDewasa', compact('dataDewasa'));
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


    public function create()
    {
        // Get last ID and generate new registration number
        $lastId = DataDewasa::max('id') ?? 0;
        $nextId = $lastId + 1;

        // Format the registration number
        $no_reg = 'JRD' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        return view('main.admin.daftarDewasa', compact('no_reg'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'no_reg' => 'required|string',
            'nik' => 'nullable|string',
            'nama' => 'nullable|string',
            'tanggal_lahir' => 'nullable|string',
            'umur' => 'nullable|integer',
            'jenis_kelamin' => 'nullable|string',
            'alamat' => 'nullable|string',
            'rt' => 'nullable|string|max:3',
            'rw' => 'nullable|string|max:3',
            'status' => 'nullable|string',
            'ket' => 'nullable|string',
        ]);

        $validated['status'] = strtoupper($validated['status']);
        $validated['jenis_kelamin'] = strtoupper($validated['jenis_kelamin']);

        DataDewasa::create($validated);

        $tanggalAktif = \App\Models\TanggalAktif::first()->tanggal;

        // Create absen entry for the same day
        \App\Models\AbsenDewasa::create([
            'no_reg' => $validated['no_reg'],
            'nik' => $validated['nik'],
            'nama' => $validated['nama'],
            'tanggal_lahir' => $validated['tanggal_lahir'],
            'usia' => $validated['umur'],
            'alamat' => $validated['alamat'],
            'tanggal_absen' => $tanggalAktif,
            'bb' => null,
            'tb' => null,
            'lb' => null,
            'lila' => null,
            'sistole' => null,
            'diastole' => null,
            'au' => null,
            'gda' => null,
            'kol' => null,
            'note' => null,
            'bmi' => null,
            'hasil' => null,
            'status' => $validated['status'],
            'note' => $validated['ket'],
        ]);

        return redirect()->route('daftar.dewasa')
            ->with('success', 'Data berhasil ditambahkan dan auto absen untuk hari ini');
    }

    public function edit(DataDewasa $dataDewasa)
    {
        return view('main.admin.UpdateDewasa', compact('dataDewasa'));
    }

    public function update(Request $request, DataDewasa $dataDewasa)
    {
        $validated = $request->validate([
            'nik' => 'nullable|string',
            'nama' => 'nullable|string',
            'tanggal_lahir' => 'nullable|string',
            'umur' => 'nullable|integer',
            'jenis_kelamin' => 'nullable|string',
            'alamat' => 'nullable|string',
            'rt' => 'nullable|string|max:3',
            'rw' => 'nullable|string|max:3',
            'status' => 'nullable|string',
            'bmi' => 'nullable|string',
            'ket' => 'nullable|string',
        ]);

        // Ensure status and jenis_kelamin are uppercase
        if (isset($validated['status'])) {
            $validated['status'] = strtoupper($validated['status']);
        }
        if (isset($validated['jenis_kelamin'])) {
            $validated['jenis_kelamin'] = strtoupper($validated['jenis_kelamin']);
        }

        $dataDewasa->update($validated);

        return redirect()->route('edit.dewasa')
            ->with('success', 'Data berhasil diperbarui');
    }
}
