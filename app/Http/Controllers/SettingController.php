<?php

namespace App\Http\Controllers;

use App\Models\PasswordResetRequest;
use App\Models\User;
use App\Notifications\PasswordResetApproved;
use App\Notifications\PasswordResetRequested;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Gate;
use App\Rules\StrongPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /**
     * Whitelist of allowed setting keys that can be updated via the UI.
     * This prevents injection of arbitrary keys like 'app_debug', 'app_env', etc.
     *
     * @var array<string, string>  key => validation rule
     */
    private const ALLOWED_SETTINGS = [
        'store_name'            => 'string|max:255',
        'store_address'         => 'string|max:500',
        'store_phone'           => 'string|max:30',
        'store_logo'            => 'string|max:500',
        'tax_rate'              => 'numeric|min:0|max:1',
        'receipt_footer'        => 'string|max:500',
        'receipt_visibility'    => 'in:public,private',
        'approval_threshold'    => 'numeric|min:0',
        'points_rate'           => 'numeric|min:0',
        'default_tax_enabled'   => 'in:0,1,true,false',
        'store_email'           => 'email|max:255',
        'store_website'         => 'url|max:255',
        'operating_hours_start' => 'string|max:10',
        'operating_hours_end'   => 'string|max:10',
    ];

    /**
     * Display settings page.
     */
    public function index()
    {
        Gate::authorize('manage_settings');

        $settings = \App\Models\Setting::pluck('value', 'key');

        return view('settings.index', compact('settings'));
    }

    /**
     * Update settings with strict key whitelist.
     *
     * BEFORE (VULNERABLE):
     *   Accepted ALL request fields and saved them as settings.
     *   An attacker could inject keys like 'app_debug' or 'app_env'.
     *
     * AFTER (SECURE):
     *   Only whitelisted keys from ALLOWED_SETTINGS are accepted.
     *   Each value is validated against its specific rule.
     */
    public function update(Request $request)
    {
        Gate::authorize('manage_settings');

        // Build validation rules from whitelist
        $validationRules = [];
        foreach (self::ALLOWED_SETTINGS as $key => $rule) {
            $validationRules[$key] = 'nullable|' . $rule;
        }

        $validated = $request->validate($validationRules);

        // Handle logo upload separately (file, not string)
        if ($request->hasFile('store_logo')) {
            $request->validate(['store_logo' => 'image|mimes:jpg,jpeg,png|max:1024']);

            // Delete old logo if exists
            $oldLogo = \App\Models\Setting::getValue('store_logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }

            $path = $request->file('store_logo')->store('logos', 'public');
            \App\Models\Setting::setValue('store_logo', $path);
            unset($validated['store_logo']);
        }

        // Save only whitelisted settings
        foreach ($validated as $key => $value) {
            if ($value !== null && $key !== 'store_logo') {
                \App\Models\Setting::setValue($key, $value);
            }
        }

        // Invalidate settings cache
        cache()->forget('app_settings');

        Log::info('Settings updated', [
            'user_id' => auth()->id(),
            'keys'    => array_keys($validated),
        ]);

        return redirect()
            ->route('settings.index')
            ->with('success', 'Settings updated successfully.');
    }

    /**
     * Display profile page.
     */
    public function profile()
    {
        $user = auth()->user();
        $loginActivities = \App\Models\LoginActivity::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('settings.profile', compact('user', 'loginActivities'));
    }

    /**
     * Update user profile.
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $rules = [
            'name'  => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
        ];

        // Only Owner can change email
        if ($user->isOwner()) {
            $rules['email'] = 'required|email|unique:users,email,' . $user->id;
        }

        $validated = $request->validate($rules);

        $data = $request->only(['name', 'phone']);
        if ($user->isOwner()) {
            $data['email'] = $request->email;
        }

        $user->update($data);

        return back()->with('success', 'Profile updated successfully.');
    }

    /**
     * Update user password.
     */
    public function updatePassword(Request $request)
    {
        if (!auth()->user()->isOwner()) {
            abort(403, 'Hanya Owner yang dapat mengubah kata sandi. Hubungi Owner untuk perubahan password.');
        }

        $request->validate([
            'current_password' => 'required|current_password',
            'password'         => ['required', new StrongPassword, 'confirmed'],
        ]);

        $user = auth()->user();
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        Log::info('Password changed', ['user_id' => $user->id]);

        return back()->with('success', 'Password changed successfully.');
    }

    /**
     * Create a database backup using spatie/laravel-backup.
     *
     * BEFORE (CRITICAL VULNERABILITY):
     *   Used raw SHOW TABLES + SHOW CREATE TABLE + addslashes() to build SQL dump.
     *   addslashes() is NOT safe for SQL escaping — vulnerable to multi-byte injection.
     *
     * AFTER (SECURE):
     *   Delegates to spatie/laravel-backup which uses mysqldump CLI.
     *   mysqldump handles proper escaping natively.
     *   The backup is stored on the configured disk and offered as download.
     */
    public function backup()
    {
        Gate::authorize('manage_settings');

        // Auto-detect mysqldump on Windows if not configured
        $binaryPath = config('database.connections.mysql.dump.dump_binary_path');
        if (empty($binaryPath)) {
            $detected = $this->detectMysqldumpPath();
            if ($detected) {
                config(['database.connections.mysql.dump.dump_binary_path' => $detected]);
                // Also override the runtime config for spatie
                config(['database.connections.mysql.dump.dump_binary_path' => $detected]);
            }
        }

        try {
            // Run spatie backup — only DB, no files (faster for download)
            Artisan::call('backup:run', ['--only-db' => true, '--disable-notifications' => true]);

            $output = Artisan::output();
            Log::info('Database backup created', ['user_id' => auth()->id(), 'output' => $output]);

            // Find the latest backup file
            $disk = config('backup.backup.destination.disks')[0] ?? 'local';
            $backupName = config('backup.backup.name', env('APP_NAME', 'laravel-backup'));
            $files = Storage::disk($disk)->files($backupName);

            // Filter for .sql or .zip files, sort by newest
            $sqlFiles = collect($files)
                ->filter(fn($f) => preg_match('/\.(sql|zip)$/', $f))
                ->sort()
                ->reverse()
                ->first();

            if (!$sqlFiles) {
                return redirect()
                    ->route('settings.index')
                    ->with('error', 'Backup created but file not found. Check storage/app/backups.');
            }

            return Storage::disk($disk)->download($sqlFiles);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            Log::error('Backup failed', [
                'user_id' => auth()->id(),
                'error'   => $msg,
            ]);

            // Provide user-friendly message for common mysqldump issues
            if (str_contains($msg, 'mysqldump') && str_contains($msg, 'not recognized')) {
                $msg = 'mysqldump tidak ditemukan. Coba atur DB_DUMP_BINARY_PATH di file .env dengan path folder MySQL bin (contoh: C:\\xampp\\mysql\\bin\\)';
            }

            return redirect()
                ->route('settings.index')
                ->with('error', 'Backup gagal: ' . $msg);
        }
    }

    /**
     * Auto-detect mysqldump.exe on Windows in common installation paths.
     */
    private function detectMysqldumpPath(): ?string
    {
        if (PHP_OS_FAMILY !== 'Windows') {
            return null;
        }

        $commonPaths = [
            'C:\\xampp\\mysql\\bin\\',
            'C:\\laragon\\bin\\mysql\\mysql-8.0.30-winx64\\bin\\',
            'C:\\laragon\\bin\\mysql\\mysql-8.0.31-winx64\\bin\\',
            'C:\\laragon\\bin\\mysql\\mysql-8.0.32-winx64\\bin\\',
            'C:\\laragon\\bin\\mysql\\mysql-8.0.33-winx64\\bin\\',
            'C:\\laragon\\bin\\mysql\\mysql-8.0.34-winx64\\bin\\',
            'C:\\laragon\\bin\\mysql\\mysql-8.0.35-winx64\\bin\\',
            'C:\\laragon\\bin\\mysql\\mysql-8.0.36-winx64\\bin\\',
            'C:\\laragon\\bin\\mysql\\mysql-8.0.37-winx64\\bin\\',
            'C:\\wamp64\\bin\\mysql\\mysql8.0.30\\bin\\',
            'C:\\wamp64\\bin\\mysql\\mysql8.0.31\\bin\\',
            'C:\\wamp64\\bin\\mysql\\mysql8.0.32\\bin\\',
            'C:\\wamp64\\bin\\mysql\\mysql8.0.33\\bin\\',
            'C:\\wamp64\\bin\\mysql\\mysql8.0.34\\bin\\',
            'C:\\wamp64\\bin\\mysql\\mysql8.0.35\\bin\\',
            'C:\\Program Files\\MySQL\\MySQL Server 8.0\\bin\\',
            'C:\\Program Files\\MySQL\\MySQL Server 8.4\\bin\\',
            'C:\\Program Files\\MySQL\\MySQL Server 9.0\\bin\\',
        ];

        foreach ($commonPaths as $path) {
            if (file_exists($path . 'mysqldump.exe')) {
                return $path;
            }
        }

        return null;
    }

    /**
     * Database restore is DISABLED for security reasons.
     *
     * BEFORE (CRITICAL VULNERABILITY):
     *   Accepted uploaded SQL files and executed them via DB::unprepared().
     *   The "APMS header" check and forbidden-command blacklist were trivially bypassable.
     *   An attacker could upload crafted SQL to create admin accounts, drop tables,
     *   exfiltrate data, or execute OS commands via INTO OUTFILE.
     *
     * AFTER (SECURE):
     *   Restore is removed from the web interface entirely.
     *   Database restore should ONLY be performed via CLI by a system administrator:
     *     php artisan backup:restore <backup-file>
     *   Or directly via: mysql -u apms_user -p systemasharparfum < backup.sql
     *
     * If you need a web-based restore, implement a JSON-based format with
     * schema validation, not raw SQL execution.
     */
    public function restore(Request $request)
    {
        Log::warning('Restore attempt blocked — web restore is disabled for security.', [
            'user_id' => auth()->id(),
            'ip'      => $request->ip(),
        ]);

        return redirect()
            ->route('settings.index')
            ->with(
                'error',
                'Database restore via web interface is disabled for security. '
                . 'Use CLI: php artisan backup:restore or mysql -u <user> -p <database> < backup.sql'
            );
    }

    /**
     * Submit a password reset request (for non-owner users).
     */
    public function requestPasswordReset(Request $request)
    {
        if (auth()->user()->isOwner()) {
            abort(403, 'Owner tidak perlu request reset password.');
        }

        $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        $existing = PasswordResetRequest::where('user_id', auth()->id())
            ->where('status', 'pending')->first();

        if ($existing) {
            return back()->with('error', 'Anda masih memiliki permintaan reset yang pending. Harap tunggu hingga Owner merespon.');
        }

        $resetRequest = PasswordResetRequest::create([
            'user_id' => auth()->id(),
            'status' => 'pending',
            'notes' => $request->notes,
        ]);

        // Notify all Owner users
        $owners = User::where('role', 'owner')->get();
        foreach ($owners as $owner) {
            $owner->notify(new PasswordResetRequested(auth()->user(), $resetRequest));
        }

        Log::info('Password reset requested', ['user_id' => auth()->id()]);

        return back()->with('success', 'Permintaan reset password telah dikirim ke Owner. Silakan tunggu konfirmasi.');
    }

    /**
     * Show password reset requests (owner only).
     */
    public function resetRequests()
    {
        Gate::authorize('manage_employees');

        $pendingRequests = PasswordResetRequest::with(['user.branch'])->pending()->latest()->get();
        $resolvedRequests = PasswordResetRequest::with(['user', 'resolver'])
            ->where('status', '!=', 'pending')->latest()->paginate(20);

        return view('settings.reset-requests', compact('pendingRequests', 'resolvedRequests'));
    }

    /**
     * Approve and generate new password (owner only).
     */
    public function approveReset(Request $request, PasswordResetRequest $resetRequest)
    {
        Gate::authorize('manage_employees');

        if ($resetRequest->status !== 'pending') {
            return back()->with('error', 'Permintaan ini sudah diproses.');
        }

        $newPassword = \Illuminate\Support\Str::random(16);

        $resetRequest->update([
            'status' => 'approved',
            'new_password' => Hash::make($newPassword),
            'resolved_by' => auth()->id(),
            'resolved_at' => now(),
        ]);

        $user = $resetRequest->user;
        $user->update([
            'password' => Hash::make($newPassword),
        ]);

        // Notify the user
        $user->notify(new PasswordResetApproved($resetRequest, $newPassword));

        Log::info('Password reset approved', [
            'user_id' => $user->id,
            'resolved_by' => auth()->id(),
        ]);

        return back()->with('success', 'Password baru untuk ' . $user->name . ' telah dikirim.');
    }

    /**
     * Reject a password reset request (owner only).
     */
    public function rejectReset(Request $request, PasswordResetRequest $resetRequest)
    {
        Gate::authorize('manage_employees');

        if ($resetRequest->status !== 'pending') {
            return back()->with('error', 'Permintaan ini sudah diproses.');
        }

        $resetRequest->update([
            'status' => 'rejected',
            'resolved_by' => auth()->id(),
            'resolved_at' => now(),
        ]);

        return back()->with('success', 'Permintaan reset password ditolak.');
    }
}
