<?php

use App\Models\TanggalAktif;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CekAbsenController;
use App\Http\Controllers\AbsenDewasaController;
use App\Http\Controllers\DaftarDewasaController;
use Illuminate\Support\Facades\Auth;

// Move root route outside middleware groups and add role checking
Route::get('/', function () {
    if (!Auth::check()) {
        return redirect('login');
    }

    $user = Auth::user();

    // Redirect based on role
    if ($user->role === 1) {
        return redirect()->route('formDewasa');
    } elseif ($user->role === 2) {
        return redirect()->route('formDarah');
    }

    // For role 0 (admin), set up the dashboard
    if (!session()->has('tanggal')) {
        session(['tanggal' => now()->format('Y-m-d')]);
    }

    if (!session()->has('nomor')) {
        session(['nomor' => 0]);
    }

    if (!TanggalAktif::exists()) {
        TanggalAktif::create([
            'tanggal' => now()->format('Y-m-d')
        ]);
    } else {
        $tanggalAktif = TanggalAktif::first();
        $tanggalHariIni = now()->format('Y-m-d');

        if ($tanggalAktif->tanggal !== $tanggalHariIni) {
            $tanggalAktif->update(['tanggal' => $tanggalHariIni]);
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

    Route::get('/AbsenDewasa', [AbsenDewasaController::class, 'index'])->name('absen.dewasa');
    Route::get('/EditAbsenDewasa', [AbsenDewasaController::class, 'index2'])->name('edit.absen.dewasa');
    Route::post('/AbsenDewasa', [AbsenDewasaController::class, 'store'])->name('absen.dewasa.store');
    Route::get('/SearchAbsenDewasa', [AbsenDewasaController::class, 'search'])->name('search.absen.dewasa');
    Route::get('/AbsenDewasa/{id}/edit', [AbsenDewasaController::class, 'edit'])->name('absen.dewasa.edit');
    Route::put('/AbsenDewasa/{id}', [AbsenDewasaController::class, 'update'])->name('absen.dewasa.update');


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

        session(['tanggal' => $request->tanggal]);
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

    Route::get('/formAnak', function () {
        return view('main.formAnak');
    })->name('formAnak');
});


Route::middleware(['auth', 'verified', 'role:2'])->group(function () {
    Route::get('/formDarah', [AbsenDewasaController::class, 'indexIsiDarah'])->name('formDarah');
    Route::put('/formDarah/{id}/isi-darah', [AbsenDewasaController::class, 'isiDarah'])->name('formDarah.isi-darah');
});

Route::middleware(['auth', 'verified', 'role:3'])->group(function () {
    Route::get('/formNote', function () {
        return view('main.formNote');
    })->name('formNote');
});


require __DIR__ . '/auth.php';
