<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::middleware(['auth', 'verified', 'role:0'])->group(function () {
    Route::get('/', function () {
        return view('dashboard');
    })->name('dashboard');
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


require __DIR__.'/auth.php';
