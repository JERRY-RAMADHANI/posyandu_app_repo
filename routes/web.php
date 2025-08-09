<?php

use App\Http\Controllers\CekAbsenController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::middleware(['auth', 'verified', 'role:0'])->group(function () {
    Route::get('/', function () {
        if (!session()->has('tanggal')) {
            session(['tanggal' => now()->format('Y-m-d')]);
        }

        if (!session()->has('nomor')) {
            session(['nomor' => 1]);
        }

        return view('main.admin.dashboard', [
            'nomor' => session('nomor')
        ]);
    })->name('dashboard');

    Route::get('/DaftarDewasa', function () {
        dd(session()->all());
    })->name('daftar.dewasa');

    Route::post('/nomor/next', function () {
        $next = session('nomor', 1) + 1;
        session(['nomor' => $next]);
        return back();
    })->name('nomor.next');

    Route::post('/nomor/reset', function () {
        session(['nomor' => 1]);
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
    Route::get('/formDewasa', function () {
        return view('main.formDewasa');
    })->name('formDewasa');

    Route::get('/formAnak', function () {
        return view('main.formAnak');
    })->name('formAnak');
});


Route::middleware(['auth', 'verified', 'role:2'])->group(function () {
    Route::get('/formDarah', function () {
        return view('formDarah');
    })->name('formDarah');
});


require __DIR__ . '/auth.php';
