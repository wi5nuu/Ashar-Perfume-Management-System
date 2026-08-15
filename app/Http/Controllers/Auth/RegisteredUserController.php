<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\StrongPassword;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        if (! config('security.registration.enabled', false)) {
            return back()->withErrors(['email' => 'Pendaftaran sedang ditutup. Silakan hubungi administrator.'])->onlyInput('email');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', new StrongPassword],
        ]);

        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = 'cashier';
        // password di-set eksplisit (bukan mass-assignment) — 'hashed' cast
        // di model meng-hash otomatis; password sengaja tidak ada di $fillable.
        $user->password = $request->password;
        $user->save();

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
