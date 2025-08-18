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
            'no_reg' => 'required|string', // tambahkan validasi no_reg
            'nik' => 'nullable|string',
            'nama' => 'nullable|string',
            'tanggal_lahir' => 'nullable|string',
            'usia' => 'nullable|integer',
            'jenis_kelamin' => 'nullable|string',
            'alamat' => 'nullable|string',
            'rt' => 'nullable|string|max:3',
            'rw' => 'nullable|string|max:3',
            'nama_ortu' => 'nullable|string',
        ]);

        $validated['jenis_kelamin'] = strtoupper($validated['jenis_kelamin']);

        DataBalita::create($validated);

        return redirect()->route('daftar.balita')
            ->with('success', 'Data berhasil ditambahkan');
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
            'nama_ortu' => 'nullable|string',
            'bmi' => 'nullable|string',
            'keterangan' => 'nullable|string'
        ]);

        // Ensure status and jenis_kelamin are uppercase
        if (isset($validated['jenis_kelamin'])) {
            $validated['jenis_kelamin'] = strtoupper($validated['jenis_kelamin']);
        }

        $dataBalita->update($validated);

        return redirect()->route('edit.balita')
            ->with('success', 'Data berhasil diperbarui');
    }
}
