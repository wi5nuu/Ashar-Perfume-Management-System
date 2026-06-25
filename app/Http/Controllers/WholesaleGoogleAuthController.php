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
        session()->put('state', $state = \Illuminate\Support\Str::random(40));
        return Socialite::driver('google')->with(['state' => $state])->redirect();
    }

    public function callback(Request $request)
    {
        $state = session()->pull('state');
        if (!$state || $request->input('state') !== $state) {
            return redirect()->route('wholesale.customer.login')
                ->with('error', 'Session tidak valid. Silakan coba lagi.');
        }

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
                ->with('error', 'Akun dengan email tersebut tidak terdaftar. Hubungi Owner.');
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('wholesale.customer.dashboard'));
    }
}
