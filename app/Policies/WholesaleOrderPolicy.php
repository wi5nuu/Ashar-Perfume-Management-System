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

    protected function hasBranchAccess(User $user, WholesaleOrder $order): bool
    {
        return $user->isOwner() || $user->isAdminPusat() || $user->branch_id === $order->branch_id;
    }

    public function view(User $user, WholesaleOrder $order): bool
    {
        return $user->hasPermission('wholesale.view') && $this->hasBranchAccess($user, $order);
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('wholesale.manage');
    }

    public function update(User $user, WholesaleOrder $order): bool
    {
        return $user->hasPermission('wholesale.manage') && $this->hasBranchAccess($user, $order);
    }

    public function delete(User $user, WholesaleOrder $order): bool
    {
        return $user->hasPermission('wholesale.manage') && $this->hasBranchAccess($user, $order);
    }

    public function confirm(User $user, WholesaleOrder $order): bool
    {
        return $user->hasPermission('wholesale.manage') && $this->hasBranchAccess($user, $order);
    }

    public function process(User $user, WholesaleOrder $order): bool
    {
        return $user->hasPermission('wholesale.manage') && $this->hasBranchAccess($user, $order);
    }

    public function pack(User $user, WholesaleOrder $order): bool
    {
        return $user->hasPermission('wholesale.manage') && $this->hasBranchAccess($user, $order);
    }

    public function ship(User $user, WholesaleOrder $order): bool
    {
        return $user->hasPermission('wholesale.manage') && $this->hasBranchAccess($user, $order);
    }

    public function deliver(User $user, WholesaleOrder $order): bool
    {
        return $user->hasPermission('wholesale.manage') && $this->hasBranchAccess($user, $order);
    }

    public function cancel(User $user, WholesaleOrder $order): bool
    {
        return $user->hasPermission('wholesale.manage') && $this->hasBranchAccess($user, $order);
    }

    public function print(User $user, WholesaleOrder $order): bool
    {
        return $user->hasPermission('wholesale.view') && $this->hasBranchAccess($user, $order);
    }
}
