<?php

namespace App\Listeners;

use App\Mail\LoginNotification;
use App\Models\KnownDevice;
use App\Services\Security\ActivityMonitor;
use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class RecordLoginActivity
{
    public function __construct(
        protected Request $request,
        protected ActivityMonitor $monitor,
    ) {}

    public function handle(Login $event): void
    {
        $user = $event->user;
        $ip = $this->request->ip();
        $ua = $this->request->userAgent() ?? '';
        $isSuspicious = $this->monitor->isSuspiciousLogin($user, $ip);

        $user->recordLogin([
            'ip' => $ip,
            'user_agent' => $ua,
            'is_suspicious' => $isSuspicious,
        ]);

        if (!KnownDevice::isKnown($user->id, $ip, $ua)) {
            KnownDevice::register($user->id, $ip, $ua);

            if (config('security.login_notification.notify_on_new_device', true) && $user->email) {
                try {
                    Mail::to($user->email)->send(new LoginNotification(
                        user: $user,
                        ipAddress: $ip,
                        userAgent: $ua,
                        time: now()->format('d M Y H:i:s'),
                    ));
                } catch (\Throwable $e) {
                    Log::error('Gagal mengirim notifikasi login', ['error' => $e->getMessage()]);
                }
            }
        }

        if ($isSuspicious) {
            Log::warning("Suspicious login detected for {$user->email} from IP {$ip}");
        }
    }
}
