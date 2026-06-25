<?php

use App\Http\Controllers\Admin\MonitoringController;
use App\Http\Controllers\Admin\RbacController;
use App\Http\Controllers\Admin\SecurityController;
use App\Http\Controllers\Admin\TwoFactorController;
use App\Services\Security\ActivityMonitor;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'throttle:60,1'])->prefix('admin')->name('admin.')->group(function () {

    // RBAC Management — roles.manage permission required
    Route::middleware(['can:roles.manage'])->prefix('rbac')->name('rbac.')->group(function () {
        Route::get('/', [RbacController::class, 'index'])->name('index');
        Route::get('/roles/{role}', [RbacController::class, 'show'])->name('show');
        Route::post('/roles/{role}/permissions', [RbacController::class, 'syncPermissions'])->name('sync-permissions');
        Route::get('/roles/{role}/users', [RbacController::class, 'users'])->name('users');
        Route::post('/roles/{role}/users', [RbacController::class, 'assignUser'])->name('assign-user');
        Route::delete('/roles/{role}/users/{user}', [RbacController::class, 'removeUser'])->name('remove-user');
        Route::get('/users/{user}/permissions', [RbacController::class, 'userPermissions'])->name('user-permissions');
        Route::post('/users/{user}/permissions', [RbacController::class, 'syncUserPermissions'])->name('sync-user-permissions');
    });

    // Security Dashboard & View-only routes — audit.view permission
    Route::middleware(['can:audit.view'])->prefix('security')->name('security.')->group(function () {
        Route::get('/', [SecurityController::class, 'securityOverview'])->name('overview');
        Route::get('/audit-logs', [SecurityController::class, 'auditLogs'])->name('audit-logs');
        Route::get('/login-activities', [SecurityController::class, 'loginActivities'])->name('login-activities');
        Route::get('/locked-accounts', [SecurityController::class, 'lockedAccounts'])->name('locked-accounts');
        Route::get('/blocked-ips', [SecurityController::class, 'blockedIps'])->name('blocked-ips');
        Route::get('/integrity-check', [SecurityController::class, 'integrityCheck'])->name('integrity');

        // Sensitive operations — manage_settings permission required
        Route::middleware(['can:manage_settings'])->group(function () {
            Route::post('/accounts/{user}/unlock', [SecurityController::class, 'unlockAccount'])->name('unlock-account');
            Route::post('/accounts/{user}/force-logout', [SecurityController::class, 'forceLogout'])->name('force-logout');
            Route::post('/ips/{ip}/unblock', [SecurityController::class, 'unblockIp'])->name('unblock-ip');
        });
    });

    // 2FA Management — self-service, no extra permission
    Route::prefix('security')->name('security.')->group(function () {
        Route::get('/two-factor', [TwoFactorController::class, 'show'])->name('two-factor');
        Route::post('/two-factor/confirm', [TwoFactorController::class, 'confirm'])->name('two-factor.confirm');
        Route::post('/two-factor/disable', [TwoFactorController::class, 'disable'])->name('two-factor.disable');
    });

    // Monitoring (Backup & Logs) — manage_settings permission
    Route::middleware(['can:manage_settings'])->prefix('monitoring')->name('monitoring.')->group(function () {
        Route::get('/backup', [MonitoringController::class, 'backupIndex'])->name('backup');
        Route::post('/backup/create', [MonitoringController::class, 'backupCreate'])->name('backup.create');
        Route::delete('/backup/{filename}', [MonitoringController::class, 'backupDelete'])->name('backup.delete');
        Route::get('/backup/{filename}/download', [MonitoringController::class, 'backupDownload'])->name('backup.download');
        Route::get('/logs', [MonitoringController::class, 'logViewer'])->name('logs');
    });

});

// Password change (must be accessible when session forces it)
Route::middleware(['auth'])->group(function () {
    Route::get('/password/change', [SecurityController::class, 'passwordChangeForm'])->name('password.change.form');
    Route::post('/password/change', [SecurityController::class, 'passwordChange'])->name('password.change');
});

// Cron-style route for audit cleanup (admin only, should be replaced by scheduler)
Route::middleware(['auth', 'can:manage_settings'])->post('/admin/security/cleanup-logs', function () {
    $deleted = app(ActivityMonitor::class)->cleanOldLogs();
    return redirect()->route('admin.security.overview')
        ->with('success', "{$deleted} log lama berhasil dibersihkan.");
})->name('admin.security.cleanup-logs');
