<?php

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;

class TransactionPolicy
{
    /**
     * Owner bypasses all checks (cross-branch).
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isOwner()) {
            return true;
        }

        return null;
    }

    /**
     * View any transaction within the user's branch.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'cashier', 'manager']);
    }

    /**
     * View a specific transaction — must belong to user's branch.
     */
    public function view(User $user, Transaction $transaction): bool
    {
        return $transaction->branch_id === $user->branch_id;
    }

    /**
     * Create transactions — must have a branch assigned.
     */
    public function create(User $user): bool
    {
        return $user->branch_id !== null
            && in_array($user->role, ['admin', 'cashier', 'manager']);
    }

    /**
     * Update/void a transaction — branch-scoped, admin/manager only.
     */
    public function update(User $user, Transaction $transaction): bool
    {
        return $transaction->branch_id === $user->branch_id
            && in_array($user->role, ['admin', 'manager']);
    }

    /**
     * Delete (void) a transaction — branch-scoped, admin/manager only.
     */
    public function delete(User $user, Transaction $transaction): bool
    {
        return $transaction->branch_id === $user->branch_id
            && in_array($user->role, ['admin', 'manager']);
    }
}
