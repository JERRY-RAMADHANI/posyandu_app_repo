<?php

namespace App\Http\Controllers\Auth;

use Illuminate\View\View;
use App\Models\TanggalAktif;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Auth\LoginRequest;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = Auth::user();

        if ($user->role === 0) {
            return redirect()->route('dashboard');
        } elseif ($user->role === 1) {
            return redirect()->route('formDewasa');
        } elseif ($user->role === 2) {
            return redirect()->route('formDarah');
        } elseif ($user->role === 3) {
            return redirect()->route('formNote');
        }


        return redirect('/');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // \App\Models\TanggalAktif::truncate();

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
