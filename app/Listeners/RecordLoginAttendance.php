<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Auth\Events\Login;
use App\Models\Attendance;
use Carbon\Carbon;

class RecordLoginAttendance
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        $user = $event->user;

        // Trigger manual attendance popup for 'cashier' role
        if ($user->role === 'cashier') {
            session()->put('needs_attendance', true);
        }
    }
}
