<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Rules\StrongPassword;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', new StrongPassword, 'confirmed'],
        ]);

        // password di-set eksplisit (bukan mass-assignment) — password sengaja
        // tidak ada di $fillable model User; 'hashed' cast meng-hash otomatis.
        $request->user()->forceFill([
            'password' => $validated['password'],
        ])->save();

        return back()->with('status', 'password-updated');
    }
}
