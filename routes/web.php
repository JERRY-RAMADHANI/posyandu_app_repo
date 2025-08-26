<?php

use App\Models\TanggalAktif;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CekAbsenController;
use App\Http\Controllers\AbsenBalitaController;
use App\Http\Controllers\AbsenDewasaController;
use App\Http\Controllers\DaftarBalitaController;
use App\Http\Controllers\DaftarDewasaController;
use App\Http\Controllers\AbsensiBalitaController;
use App\Http\Controllers\ExportController;

// Move root route outside middleware groups and add role checking
Route::get('/', function () {
    if (!Auth::check()) {
        return redirect('login');
    }

    $user = Auth::user();

    if ($user->role === 1) {
        return redirect()->route('formDewasa');
    } elseif ($user->role === 2) {
        return redirect()->route('formDarah');
    } elseif ($user->role === 3) {
        return redirect()->route('formNote');
    }

    // For role 0 (admin), set up the dashboard
    if (!session()->has('tanggal')) {
        session(['tanggal' => now()->format('Y-m-d')]);
    }

    if (!session()->has('nomor')) {
        session(['nomor' => 0]);
    }

    if (session('changed', false) === false) {
        $today = now()->format('Y-m-d');
        session(['tanggal' => $today]);

        if (TanggalAktif::exists()) {
            TanggalAktif::truncate();
            TanggalAktif::create(['tanggal' => $today]);
        } else {
            TanggalAktif::create(['tanggal' => $today]);
        }
    } else {
        $tanggalAktif = TanggalAktif::first();
        if ($tanggalAktif) {
            session(['tanggal' => $tanggalAktif->tanggal]);
        }
    }


    return view('main.admin.dashboard', [
        'nomor' => session('nomor')
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

// Route::get('/', function () {
//     return view('welcome');
// });

Route::middleware(['auth', 'verified', 'role:0'])->group(function () {
    Route::get('/EditDewasa', [DaftarDewasaController::class, 'index'])->name('edit.dewasa');
    Route::get('/DaftarDewasa', [DaftarDewasaController::class, 'create'])->name('daftar.dewasa');
    Route::post('/DaftarDewasa/create', [DaftarDewasaController::class, 'store'])->name('daftar.dewasa.store');
    Route::get('/SearchDewasa', [DaftarDewasaController::class, 'search'])->name('search.dewasa');
    Route::get('/UpdateDewasa/{dataDewasa}/edit', [DaftarDewasaController::class, 'edit'])->name('daftar.dewasa.edit');
    Route::put('/UpdateDewasa/{dataDewasa}', [DaftarDewasaController::class, 'update'])->name('daftar.dewasa.update');

    Route::get('/EditBalita', [DaftarBalitaController::class, 'index'])->name('edit.balita');
    Route::get('/DaftarBalita', [DaftarBalitaController::class, 'create'])->name('daftar.balita');
    Route::post('/DaftarBalita/create', [DaftarBalitaController::class, 'store'])->name('daftar.balita.store');
    Route::get('/SearchBalita', [DaftarBalitaController::class, 'search'])->name('search.balita');
    Route::get('/UpdateBalita/{dataBalita}/edit', [DaftarBalitaController::class, 'edit'])->name('daftar.balita.edit');
    Route::put('/UpdateBalita/{dataBalita}', [DaftarBalitaController::class, 'update'])->name('daftar.balita.update');

    Route::get('/AbsenDewasa', [AbsenDewasaController::class, 'index'])->name('absen.dewasa');
    Route::get('/EditAbsenDewasa', [AbsenDewasaController::class, 'index2'])->name('edit.absen.dewasa');
    Route::post('/AbsenDewasa', [AbsenDewasaController::class, 'store'])->name('absen.dewasa.store');
    Route::get('/SearchAbsenDewasa', [AbsenDewasaController::class, 'search'])->name('search.absen.dewasa');
    Route::get('/AbsenDewasa/{id}/edit', [AbsenDewasaController::class, 'edit'])->name('absen.dewasa.edit');
    Route::put('/AbsenDewasa/{id}', [AbsenDewasaController::class, 'update'])->name('absen.dewasa.update');

    Route::get('/AbsenBalita', [AbsensiBalitaController::class, 'index'])->name('absen.balita');
    Route::get('/EditAbsenBalita', [AbsensiBalitaController::class, 'index2'])->name('edit.absen.balita');
    Route::post('/AbsenBalita', [AbsensiBalitaController::class, 'store'])->name('absen.balita.store');
    Route::get('/SearchAbsenBalita', [AbsensiBalitaController::class, 'search'])->name('search.absen.balita');
    Route::get('/AbsenBalita/{id}/edit', [AbsensiBalitaController::class, 'edit'])->name('absen.balita.edit');
    Route::put('/AbsenBalita/{id}', [AbsensiBalitaController::class, 'update'])->name('absen.balita.update');

    Route::get('CetakLaporan', function () {
        return view('main.admin.CetakLaporan');
    })->name('CetakLaporan');


    Route::get('Export/AbsenBalita', [ExportController::class, 'exportAbsenBalita'])->name('Export.AbsenBalita');

    Route::get('Export/AbsenDewasa', [ExportController::class, 'exportAbsenDewasa'])->name('Export.AbsenDewasa');

    Route::get('Export/AbsenPUS', [ExportController::class, 'exportAbsenPUS'])->name('Export.AbsenPUS');

    Route::get('Export/P3', [ExportController::class, 'exportP3'])->name('Export.P3');

    Route::get('/session/check', function () {
        // Ambil session tertentu
        $sessionData = [
            'tanggal' => session('tanggal', null),
            'nomor' => session('nomor', null),
            'changed' => session('changed', false),
        ];

        // Return sebagai JSON biar gampang dicek di browser / Postman
        return response()->json($sessionData);
    })->middleware(['auth'])->name('session.check');




    Route::post('/nomor/next', function () {
        $next = session('nomor', 1) + 1;
        session(['nomor' => $next]);
        return back()->with('call_with_intro', true);
    })->name('nomor.next');

    Route::post('/nomor/reset', function () {
        session(['nomor' => 0]);
        return back();
    })->name('nomor.reset');

    Route::post('/nomor/set', function () {
        $set = (int) request('nomor');
        session(['nomor' => $set]);
        return back()->with('skip_intro', true);
    })->name('nomor.set');

    Route::post('/tanggal/set', function (Illuminate\Http\Request $request) {
        $request->validate([
            'tanggal' => 'required|date',
        ]);

        if (TanggalAktif::exists()) {
            TanggalAktif::truncate();
        }

        TanggalAktif::create([
            'tanggal' => $request->tanggal
        ]);

        session([
            'tanggal' => $request->tanggal,
            'changed' => true
        ]);
        return back()->with('success', 'Tanggal sukses diubah!');
    })->name('tanggal.set');

    Route::get('/CekAbsen', [CekAbsenController::class, 'index'])->name('CekAbsen');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified', 'role:1'])->group(function () {
    Route::get('/formDewasa', [AbsenDewasaController::class, 'indexIsiBB'])->name('formDewasa');
    Route::put('/formDewasa/{id}/isi-bb', [AbsenDewasaController::class, 'isiBB'])->name('formDewasa.isi-bb');

    Route::get('/formAnak', [AbsensiBalitaController::class, 'indexIsiBB'])->name('formAnak');
    Route::put('/formAnak/{id}/isi-bb', [AbsensiBalitaController::class, 'isiBB'])->name('formAnak.isi-bb');
});


Route::middleware(['auth', 'verified', 'role:2'])->group(function () {
    Route::get('/formDarah', [AbsenDewasaController::class, 'indexIsiDarah'])->name('formDarah');
    Route::put('/formDarah/{id}/isi-darah', [AbsenDewasaController::class, 'isiDarah'])->name('formDarah.isi-darah');
});

Route::middleware(['auth', 'verified', 'role:3'])->group(function () {
    Route::get('/formNote', [AbsensiBalitaController::class, 'indexIsiKet'])->name('formNote');
    Route::put('/formNote/{id}/isi-note', [AbsensiBalitaController::class, 'isiKet'])->name('formNote.isi-ket');
});


require __DIR__ . '/auth.php';
