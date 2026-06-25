<?php

namespace App\Policies;

use App\Models\WholesaleOrder;
use App\Models\User;

class WholesaleOrderPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isOwner()) return true;
        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasPermission('wholesale.view');
    }

    public function view(User $user, WholesaleOrder $order): bool
    {
        return $user->hasPermission('wholesale.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('wholesale.manage');
    }

    public function update(User $user, WholesaleOrder $order): bool
    {
        return $user->hasPermission('wholesale.manage');
    }

    public function delete(User $user, WholesaleOrder $order): bool
    {
        return $user->hasPermission('wholesale.manage');
    }

    public function confirm(User $user, WholesaleOrder $order): bool
    {
        return $user->hasPermission('wholesale.manage');
    }

    public function process(User $user, WholesaleOrder $order): bool
    {
        return $user->hasPermission('wholesale.manage');
    }

    public function pack(User $user, WholesaleOrder $order): bool
    {
        return $user->hasPermission('wholesale.manage');
    }

    public function ship(User $user, WholesaleOrder $order): bool
    {
        return $user->hasPermission('wholesale.manage');
    }

    public function deliver(User $user, WholesaleOrder $order): bool
    {
        return $user->hasPermission('wholesale.manage');
    }

    public function cancel(User $user, WholesaleOrder $order): bool
    {
        return $user->hasPermission('wholesale.manage');
    }

    public function print(User $user, WholesaleOrder $order): bool
    {
        return $user->hasPermission('wholesale.view');
    }
}
