<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class WholesaleGoogleAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('wholesale.customer.login')
                ->with('error', 'Gagal login dengan Google. Silakan coba lagi.');
        }

        $user = User::where('email', $googleUser->email)
            ->where('role', 'wholesale_customer')
            ->where('is_active', true)
            ->first();

        if (!$user) {
            return redirect()->route('wholesale.customer.login')
                ->with('error', 'Akun dengan email ' . $googleUser->email . ' tidak ditemukan. Hubungi Owner untuk mendaftarkan akun Anda.');
        }

        Auth::login($user);
        request()->session()->regenerate();

        return redirect()->intended(route('wholesale.customer.dashboard'));
    }
}
