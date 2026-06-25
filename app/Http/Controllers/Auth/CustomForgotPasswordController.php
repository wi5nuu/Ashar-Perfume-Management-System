<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordResetRequest;
use App\Models\User;
use App\Notifications\PasswordResetApproved;
use App\Notifications\PasswordResetRequested;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class CustomForgotPasswordController extends Controller
{
    const AUTO_RESET_TIMEOUT = 3600; // 1 jam
    const OPERATIONAL_START = 9;  // 09:00
    const OPERATIONAL_END   = 21; // 21:00

    public function create()
    {
        return view('auth.custom-forgot-password', [
            'password'          => null,
            'statusType'        => null,
            'statusMessage'     => null,
            'remainingMinutes'  => null,
            'withinHours'       => $this->isWithinOperationalHours(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email|max:255',
        ]);

        $user = User::where('email', $request->email)->first();

        $existing = PasswordResetRequest::where('user_id', $user->id)
            ->latest()
            ->first();

        $password         = null;
        $statusType       = null;
        $statusMessage    = null;
        $remainingMinutes = null;
        $withinHours      = $this->isWithinOperationalHours();

        if ($existing && $existing->status === 'approved') {
            $password   = $existing->new_password;
            $statusType = 'approved';
        } elseif ($existing && $existing->status === 'pending') {
            $elapsed = now()->diffInSeconds($existing->created_at);

            if ($elapsed >= self::AUTO_RESET_TIMEOUT) {
                if ($withinHours) {
                    $newPassword = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#'), 0, 12);

                    $existing->update([
                        'status'       => 'approved',
                        'new_password' => $newPassword,
                        'resolved_at'  => now(),
                        'notes'        => $existing->notes . ' | Auto-reset setelah ' . round($elapsed / 60) . ' menit',
                    ]);

                    $user->update([
                        'password' => Hash::make($newPassword),
                    ]);

                    Log::info('Password auto-reset', ['user_id' => $user->id]);

                    $password   = $newPassword;
                    $statusType = 'auto_approved';
                } else {
                    $statusType = 'outside_hours';
                }
            } else {
                $remainingSeconds  = self::AUTO_RESET_TIMEOUT - $elapsed;
                $remainingMinutes  = ceil($remainingSeconds / 60);
                $statusType        = 'pending';
            }
        } else {
            $newRequest = PasswordResetRequest::create([
                'user_id' => $user->id,
                'status'  => 'pending',
                'notes'   => 'Permintaan dari halaman login',
            ]);

            // Notify all Owner users
            $owners = User::where('role', 'owner')->get();
            foreach ($owners as $owner) {
                $owner->notify(new PasswordResetRequested($user, $newRequest));
            }

            $remainingMinutes = ceil(self::AUTO_RESET_TIMEOUT / 60);
            $statusType       = 'created';
        }

        return view('auth.custom-forgot-password', compact(
            'password', 'statusType', 'statusMessage', 'remainingMinutes', 'withinHours'
        ));
    }

    private function isWithinOperationalHours(): bool
    {
        $hour = (int) now()->format('H');
        return $hour >= self::OPERATIONAL_START && $hour < self::OPERATIONAL_END;
    }
}
