<?php

namespace App\Notifications;

use App\Models\PasswordResetRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PasswordResetApproved extends Notification
{
    use Queueable;

    public function __construct(
        public PasswordResetRequest $resetRequest,
        public string $newPassword,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'          => 'password_reset_approved',
            'reset_request_id' => $this->resetRequest->id,
            'message'       => 'Password Anda telah direset. Silakan hubungi admin untuk mendapatkan password baru.',
        ];
    }
}
