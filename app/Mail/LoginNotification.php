<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LoginNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $ipAddress,
        public string $userAgent,
        public string $time,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Peringatan Keamanan — Login Baru ke APMS',
        );
    }

    public function content(): Content
    {
        return new Content(
            htmlString: $this->buildHtml(),
        );
    }

    private function buildHtml(): string
    {
        $name = htmlspecialchars($this->user->name);
        $ip = htmlspecialchars($this->ipAddress);
        $ua = htmlspecialchars($this->userAgent);
        $time = htmlspecialchars($this->time);

        return <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="font-family: 'Segoe UI',Arial,sans-serif; background: #f4f4f4; padding: 40px;">
<div style="max-width: 600px; margin: auto; background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 8px 24px rgba(0,0,0,0.1);">
<div style="background: linear-gradient(135deg, #FF6B35, #E55A2B); padding: 30px; text-align: center;">
<h1 style="color: #fff; margin: 0; font-size: 24px;">🔐 Peringatan Keamanan</h1>
</div>
<div style="padding: 30px;">
<h2 style="color: #2D3047; margin-top: 0;">Halo, {$name}</h2>
<p>Akun Anda baru saja digunakan untuk masuk dari perangkat baru:</p>
<table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
<tr><td style="padding: 10px; border-bottom: 1px solid #eee; color: #666;">Waktu</td><td style="padding: 10px; border-bottom: 1px solid #eee; font-weight: bold;">{$time}</td></tr>
<tr><td style="padding: 10px; border-bottom: 1px solid #eee; color: #666;">Alamat IP</td><td style="padding: 10px; border-bottom: 1px solid #eee; font-weight: bold;">{$ip}</td></tr>
<tr><td style="padding: 10px; color: #666;">Perangkat</td><td style="padding: 10px; font-weight: bold; font-size: 12px;">{$ua}</td></tr>
</table>
<p style="color: #666;">Jika ini Anda, abaikan email ini. Jika bukan, segera hubungi administrator dan ubah kata sandi Anda.</p>
<hr style="border: none; border-top: 1px solid #eee; margin: 20px 0;">
<p style="color: #999; font-size: 12px;">APMS — Ashar Parfum Management System</p>
</div>
</div>
</body>
</html>
HTML;
    }
}
