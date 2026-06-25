<?php

namespace App\Notifications;

use App\Models\PasswordResetRequest;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PasswordResetRequested extends Notification
{
    use Queueable;

    public function __construct(
        public User $requester,
        public PasswordResetRequest $resetRequest,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'           => 'password_reset_requested',
            'user_id'        => $this->requester->id,
            'user_name'      => $this->requester->name,
            'user_email'     => $this->requester->email,
            'user_role'      => $this->requester->role,
            'user_branch'    => $this->requester->branch?->name ?? 'Pusat',
            'reset_request_id' => $this->resetRequest->id,
            'notes'          => $this->resetRequest->notes,
            'message'        => "{$this->requester->name} ({$this->requester->email}) meminta reset password.",
        ];
    }
}
