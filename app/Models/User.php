<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'full_name',
        'nickname',
        'nik',
        'gender',
        'place_of_birth',
        'date_of_birth',
        'religion',
        'marital_status',
        'last_education',
        'join_date',
        'employee_id',
        'employment_status',
        'bank_name',
        'bank_account_number',
        'bank_account_holder',
        'npwp',
        'basic_salary',
        'origin',
        'email',
        'phone',
        'role',
        'branch_id',
        'skills',
        'is_staying_in_mess',
        'is_active',
        'can_login',
        'living_address',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relation',
        'referral_code',
        'referred_by_id',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password_changed_at' => 'datetime',
            'locked_until' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_secret' => 'encrypted',
            'two_factor_recovery_codes' => 'encrypted',
            'bank_account_number' => 'encrypted',
            'npwp' => 'encrypted',
            'nik' => 'encrypted',


            'can_login' => 'boolean',
            'is_locked' => 'boolean',
            'requires_password_change' => 'boolean',
            'date_of_birth' => 'date',
            'join_date' => 'date',
            'is_staying_in_mess' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Role helper methods
     */
    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isAdminPusat(): bool
    {
        return $this->role === 'admin' && is_null($this->branch_id);
    }

    public function isAdminCabang(): bool
    {
        return $this->role === 'admin' && !is_null($this->branch_id);
    }

    public function isCashier(): bool
    {
        return $this->role === 'cashier';
    }

    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    public function isSupervisor(): bool
    {
        return $this->role === 'supervisor';
    }

    public function isStoreEmployee(): bool
    {
        return !$this->can_login;
    }

    public function isSystemUser(): bool
    {
        return $this->can_login;
    }

    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role, $roles);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    // BUG-06 FIX: Relasi-relasi yang hilang
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function shifts()
    {
        return $this->hasMany(Shift::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function payrollSettings()
    {
        return $this->hasOne(PayrollSetting::class);
    }

    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user')->withTimestamps();
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'permission_user')->withTimestamps();
    }

    public function loginActivities()
    {
        return $this->hasMany(LoginActivity::class);
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    public function referrer()
    {
        return $this->belongsTo(self::class, 'referred_by_id');
    }

    public function referrals()
    {
        return $this->hasMany(self::class, 'referred_by_id');
    }

    public function hasPermission(string $slug): bool
    {
        if ($this->isOwner()) return true;

        return Cache::remember("user_perm:{$this->id}:{$slug}", 3600, function () use ($slug) {
            if ($this->permissions()->where('slug', $slug)->exists()) return true;
            foreach ($this->roles as $role) {
                if ($role->permissions()->where('slug', $slug)->exists()) return true;
            }
            return false;
        });
    }

    public function assignRole(string $slug): void
    {
        $role = Role::where('slug', $slug)->first();
        if ($role) {
            $this->roles()->syncWithoutDetaching([$role->id]);
            Cache::forget("user_perm:{$this->id}:*");
        }
    }

    public function syncRoles(array $roleSlugs): void
    {
        $roleIds = Role::whereIn('slug', $roleSlugs)->pluck('id');
        $this->roles()->sync($roleIds);
        Cache::tags(["user:{$this->id}"])->flush();
    }

    public function forceChangePassword(): void
    {
        $this->update([
            'requires_password_change' => true,
        ]);
    }

    public function lock(string $duration = '15 minutes'): void
    {
        $this->update([
            'is_locked' => true,
            'locked_until' => now()->add($duration),
        ]);
    }

    public function unlock(): void
    {
        $this->update([
            'is_locked' => false,
            'locked_until' => null,
            'login_attempts' => 0,
        ]);
    }

    public function recordLogin(array $data = []): void
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => $data['ip'] ?? request()->ip(),
            'login_attempts' => 0,
        ]);

        LoginActivity::create([
            'user_id' => $this->id,
            'ip_address' => $data['ip'] ?? request()->ip(),
            'user_agent' => $data['user_agent'] ?? request()->userAgent(),
            'is_suspicious' => $data['is_suspicious'] ?? false,
        ]);
    }

    public function isPasswordExpired(): bool
    {
        if (!$this->password_changed_at) return true;
        $maxAgeDays = config('security.password_policy.max_age_days', 90);
        return $this->password_changed_at->addDays($maxAgeDays)->isPast();
    }
}
