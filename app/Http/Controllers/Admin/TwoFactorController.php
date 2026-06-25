<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TwoFactorController extends Controller
{
    public function show()
    {
        $user = auth()->user();
        $enabled = !is_null($user->two_factor_confirmed_at);

        if (!$enabled && !$user->two_factor_secret) {
            $secret = $this->generateSecret();
            $user->forceFill(['two_factor_secret' => encrypt($secret)])->save();
            $user->refresh();
        }

        $secret = decrypt($user->two_factor_secret);
        $qrUrl = $this->getQrCodeUrl('APMS - ' . $user->email, $secret);

        return view('admin.security.two-factor', compact('enabled', 'secret', 'qrUrl', 'user'));
    }

    public function confirm(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = auth()->user();
        $secret = decrypt($user->two_factor_secret);

        if (!$this->verifyTotp($secret, $request->code)) {
            return back()->withErrors(['code' => 'Kode verifikasi tidak valid.']);
        }

        $user->forceFill([
            'two_factor_confirmed_at' => now(),
        ])->save();

        Log::info("2FA enabled for user {$user->name}");

        return redirect()->route('admin.security.two-factor')
            ->with('success', 'Autentikasi dua faktor berhasil diaktifkan.');
    }

    public function disable(Request $request)
    {
        $request->validate(['password' => 'required|current_password']);

        $user = auth()->user();
        $user->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();

        Log::info("2FA disabled for user {$user->name}");

        return redirect()->route('admin.security.two-factor')
            ->with('success', 'Autentikasi dua faktor berhasil dinonaktifkan.');
    }

    private function generateSecret(): string
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = '';
        for ($i = 0; $i < 32; $i++) {
            $secret .= $chars[random_int(0, 31)];
        }
        return $secret;
    }

    private function verifyTotp(string $secret, string $code): bool
    {
        $window = 1;
        $counter = floor(now()->timestamp / 30);

        for ($i = -$window; $i <= $window; $i++) {
            $expected = $this->generateTotp($secret, $counter + $i);
            if (hash_equals($expected, $code)) {
                return true;
            }
        }
        return false;
    }

    private function generateTotp(string $secret, int $counter): string
    {
        $hash = hash_hmac('sha1', pack('N*', 0) . pack('N*', $counter), $this->base32Decode($secret), true);
        $offset = ord($hash[19]) & 0xf;
        $code = (
            ((ord($hash[$offset]) & 0x7f) << 24) |
            ((ord($hash[$offset + 1]) & 0xff) << 16) |
            ((ord($hash[$offset + 2]) & 0xff) << 8) |
            (ord($hash[$offset + 3]) & 0xff)
        ) % 1000000;

        return str_pad($code, 6, '0', STR_PAD_LEFT);
    }

    private function base32Decode(string $data): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $data = strtoupper($data);
        $buffer = 0;
        $bitsLeft = 0;
        $output = '';

        for ($i = 0; $i < strlen($data); $i++) {
            $val = strpos($alphabet, $data[$i]);
            if ($val === false) continue;
            $buffer = ($buffer << 5) | $val;
            $bitsLeft += 5;
            if ($bitsLeft >= 8) {
                $output .= chr(($buffer >> ($bitsLeft - 8)) & 0xff);
                $bitsLeft -= 8;
            }
        }

        return $output;
    }

    private function getQrCodeUrl(string $label, string $secret): string
    {
        $issuer = 'APMS';
        $encoded = rawurlencode($issuer) . ':' . rawurlencode($label);
        $params = http_build_query([
            'secret' => $secret,
            'issuer' => $issuer,
            'algorithm' => 'SHA1',
            'digits' => 6,
            'period' => 30,
        ]);
        return "otpauth://totp/{$encoded}?{$params}";
    }
}
