<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

/*
 * Private channel: hanya owner dan admin yang boleh mendengarkan
 * notifikasi hutang baru yang masuk dari kasir.
 */
Broadcast::channel('debt-approvals', function ($user) {
    return in_array($user->role, ['owner', 'admin']);
});

/*
 * Dashboard channel: authenticated users with staff roles receive
 * live counter updates (transaction count, revenue, stock alerts, debts).
 */
Broadcast::channel('dashboard', function ($user) {
    return in_array($user->role, ['owner', 'admin', 'manager', 'cashier', 'supervisor']);
});

/*
 * Inventory updates: hanya staff yang boleh mendengar perubahan stok.
 * Scoped per branch — staff hanya terima update untuk cabang mereka sendiri.
 */
Broadcast::channel('inventory.{branchId}', function ($user, $branchId) {
    if (!in_array($user->role, ['owner', 'admin', 'manager', 'warehouse'])) {
        return false;
    }
    // Owner and admin see all branches; others only their own branch
    if (in_array($user->role, ['owner', 'admin'])) {
        return true;
    }
    return (int) $user->branch_id === (int) $branchId;
});

/*
 * Notifikasi sistem: low stock, wholesale order, debt reminder.
 * Scoped per branch — staff hanya terima notifikasi untuk cabang mereka.
 */
Broadcast::channel('notifications.{branchId}', function ($user, $branchId) {
    if (!in_array($user->role, ['owner', 'admin', 'manager'])) {
        return false;
    }
    if (in_array($user->role, ['owner', 'admin'])) {
        return true;
    }
    return (int) $user->branch_id === (int) $branchId;
});
