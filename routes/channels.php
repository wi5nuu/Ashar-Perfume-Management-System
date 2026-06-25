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
 */
Broadcast::channel('inventory', function ($user) {
    return in_array($user->role, ['owner', 'admin', 'admin_pusat', 'admin_cabang', 'manager', 'warehouse']);
});

/*
 * Notifikasi sistem: low stock, wholesale order, debt reminder.
 */
Broadcast::channel('notifications', function ($user) {
    return in_array($user->role, ['owner', 'admin', 'admin_pusat', 'admin_cabang', 'manager']);
});
