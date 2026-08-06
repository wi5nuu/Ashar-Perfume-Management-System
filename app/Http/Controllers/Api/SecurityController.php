<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LoginActivity;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class SecurityController extends Controller
{
    /**
     * Force-unlock a user account.
     * Requires the authenticated user to be owner or admin.
     */
    public function forceUnlock(User $user): JsonResponse
    {
        $this->authorizeAdminRole();

        $user->unlock();

        return response()->json([
            'status'  => 'success',
            'message' => 'User unlocked successfully.',
            'user_id' => $user->id,
        ]);
    }

    /**
     * Return the count of unique active sessions for today.
     * Requires the authenticated user to be owner or admin.
     */
    public function activeSessions(): JsonResponse
    {
        $this->authorizeAdminRole();

        $count = LoginActivity::with('user')
            ->whereDate('created_at', today())
            ->distinct('user_id')
            ->count('user_id');

        return response()->json([
            'status'          => 'success',
            'active_sessions' => $count,
            'date'            => today()->toDateString(),
        ]);
    }

    /**
     * Abort with 403 if the authenticated user is not owner or admin.
     */
    private function authorizeAdminRole(): void
    {
        $role = auth()->user()?->role;

        if (! in_array($role, ['owner', 'admin'], true)) {
            abort(403, 'Unauthorized.');
        }
    }
}
