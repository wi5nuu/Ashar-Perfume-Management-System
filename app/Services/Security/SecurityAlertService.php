<?php

namespace App\Services\Security;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SecurityAlertService
{
    private string $adminEmail;

    public function __construct()
    {
        $this->adminEmail = env('ADMIN_ALERT_EMAIL', 'admin@asharparfum.com');
    }

    public function suspiciousLogin(User $user, string $ip): void
    {
        Log::warning("SECURITY ALERT: Suspicious login for user#{$user->id} from {$ip}");

        try {
            $admins = User::whereIn('role', ['owner', 'admin'])->get();
            foreach ($admins as $admin) {
                if ($admin->email) {
                    $this->sendEmail($admin->email, 'Peringatan Keamanan: Login Mencurigakan', [
                        'user' => $user->name,
                        'email' => $user->email,
                        'ip' => $ip,
                        'time' => now()->format('d/m/Y H:i:s'),
                        'type' => 'suspicious_login',
                    ]);
                }
            }
        } catch (\Throwable $e) {
            Log::error("Failed to send security alert email: {$e->getMessage()}");
        }
    }

    public function accountLocked(User $user): void
    {
        Log::warning("SECURITY ALERT: Account locked for user#{$user->id}");

        try {
            $admins = User::whereIn('role', ['owner', 'admin'])->get();
            foreach ($admins as $admin) {
                if ($admin->email) {
                    $this->sendEmail($admin->email, 'Peringatan Keamanan: Akun Terkunci', [
                        'user' => $user->name,
                        'email' => $user->email,
                        'time' => now()->format('d/m/Y H:i:s'),
                        'type' => 'account_locked',
                    ]);
                }
            }
        } catch (\Throwable $e) {
            Log::error("Failed to send lock alert email: {$e->getMessage()}");
        }
    }

    public function bruteForceDetected(string $identifier, string $ip, int $attempts): void
    {
        Log::critical("SECURITY ALERT: Brute force detected - {$identifier} from {$ip} ({$attempts} attempts)");

        $this->sendEmail($this->adminEmail, '⚠️ PERINGATAN KRITIS: Serangan Brute Force Terdeteksi', [
            'identifier' => $identifier,
            'ip' => $ip,
            'attempts' => $attempts,
            'time' => now()->format('d/m/Y H:i:s'),
            'type' => 'brute_force',
        ]);
    }

    public function priceTamperingDetected(array $data): void
    {
        Log::critical("SECURITY ALERT: Price tampering detected", $data);

        $this->sendEmail($this->adminEmail, '⚠️ PERINGATAN: Manipulasi Harga Terdeteksi', [
            'product' => $data['product_name'] ?? 'Unknown',
            'db_price' => 'Rp ' . number_format($data['db_price'] ?? 0, 0, ',', '.'),
            'client_price' => 'Rp ' . number_format($data['client_price'] ?? 0, 0, ',', '.'),
            'user' => $data['user_id'] ?? 'Unknown',
            'time' => now()->format('d/m/Y H:i:s'),
            'type' => 'price_tampering',
        ]);
    }

    private function sendEmail(string $to, string $subject, array $data): void
    {
        try {
            $html = $this->buildEmailHtml($subject, $data);
            Mail::html($html, function ($message) use ($to, $subject) {
                $message->to($to)
                    ->subject('[APMS Security] ' . $subject)
                    ->from(env('MAIL_FROM_ADDRESS', 'security@asharparfum.com'), 'APMS Security System');
            });
        } catch (\Throwable $e) {
            Log::error("Email send failed to {$to}: {$e->getMessage()}");
        }
    }

    private function buildEmailHtml(string $subject, array $data): string
    {
        $rows = '';
        foreach ($data as $key => $value) {
            if ($key !== 'type') {
                $label = ucwords(str_replace('_', ' ', $key));
                $rows .= "<tr><td style='padding:8px;border:1px solid #ddd;font-weight:bold;'>{$label}</td>
                          <td style='padding:8px;border:1px solid #ddd;'>{$value}</td></tr>";
            }
        }

        return "<!DOCTYPE html>
        <html><head><meta charset='utf-8'></head>
        <body style='font-family:Arial;background:#f5f5f5;padding:20px;'>
            <div style='max-width:600px;margin:0 auto;background:white;border-radius:8px;overflow:hidden;'>
                <div style='background:#dc3545;color:white;padding:20px;text-align:center;'>
                    <h2 style='margin:0;'>🔒 {$subject}</h2>
                </div>
                <div style='padding:20px;'>
                    <p style='color:#666;margin-bottom:20px;'>Sistem keamanan APMS mendeteksi aktivitas berikut:</p>
                    <table style='width:100%;border-collapse:collapse;'>
                        {$rows}
                    </table>
                    <hr style='margin:20px 0;border:none;border-top:1px solid #eee;'>
                    <p style='color:#999;font-size:12px;'>
                        Ini adalah pesan otomatis dari APMS Security System.<br>
                        APMS - Ashar Parfume Management System
                    </p>
                </div>
            </div>
        </body></html>";
    }
}
