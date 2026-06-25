<?php

namespace App\Policies;

use App\Models\Expense;
use App\Models\User;

class ExpensePolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->isOwner() ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'admin_pusat', 'manager']);
    }

    public function view(User $user, Expense $expense): bool
    {
        return $expense->branch_id === $user->branch_id;
    }

    public function create(User $user): bool
    {
        return $user->branch_id !== null
            && in_array($user->role, ['admin', 'manager']);
    }

    public function update(User $user, Expense $expense): bool
    {
        return $expense->branch_id === $user->branch_id
            && in_array($user->role, ['admin', 'manager']);
    }

    public function delete(User $user, Expense $expense): bool
    {
        return $expense->branch_id === $user->branch_id
            && in_array($user->role, ['admin', 'manager']);
    }
}
