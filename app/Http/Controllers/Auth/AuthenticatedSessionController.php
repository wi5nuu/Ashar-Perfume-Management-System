<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

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

        if ($user->role === 'wholesale_customer') {
            return redirect()->route('wholesale.customer.dashboard');
        }

        $restrictedRoles = ['admin', 'cashier', 'supervisor'];

        if (in_array($user->role, $restrictedRoles)) {
            $activeShift = \App\Models\Shift::where('user_id', $user->id)
                ->where('status', 'open')
                ->first();
                
            if (!$activeShift) {
                return redirect()->route('shifts.index')->with('info', 'Silakan buka shift Anda sebelum memulai transaksi hari ini.');
            }
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
