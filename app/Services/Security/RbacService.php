<?php

namespace App\Services\Security;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;

class RbacService
{
    private const CACHE_TTL = 3600;

    public function userHasPermission(User $user, string $permissionSlug): bool
    {
        if ($user->isOwner()) return true;

        return Cache::remember("user_perms:{$user->id}:{$permissionSlug}", self::CACHE_TTL, function () use ($user, $permissionSlug) {
            $directPerms = $user->permissions()->where('slug', $permissionSlug)->exists();
            if ($directPerms) return true;

            return $user->roles()->whereHas('permissions', function ($q) use ($permissionSlug) {
                $q->where('slug', $permissionSlug);
            })->exists();
        });
    }

    public function syncRolePermissions(Role $role, array $permissionIds): void
    {
        $role->permissions()->sync($permissionIds);
        try { Cache::tags(['rbac', "role:{$role->id}"])->flush(); } catch (\Exception $e) { Cache::flush(); }
    }

    public function syncUserPermissions(User $user, array $permissionIds): void
    {
        $user->permissions()->sync($permissionIds);
        try { Cache::tags(['rbac', "user:{$user->id}"])->flush(); } catch (\Exception $e) { Cache::flush(); }
    }

    public function getUserPermissions(User $user): array
    {
        return Cache::remember("user_all_perms:{$user->id}", self::CACHE_TTL, function () use ($user) {
            $perms = collect();
            foreach ($user->roles as $role) {
                $perms = $perms->merge($role->permissions);
            }
            $perms = $perms->merge($user->permissions);
            return $perms->unique('id')->values()->toArray();
        });
    }

    public function invalidateUserCache(User $user): void
    {
        try { Cache::tags(["user:{$user->id}"])->flush(); } catch (\Exception $e) { Cache::flush(); }
    }

    public function registerGates(): void
    {
        $permissions = Cache::remember('all_permissions_slugs', self::CACHE_TTL, function () {
            return Permission::pluck('slug')->toArray();
        });

        foreach ($permissions as $slug) {
            Gate::define($slug, function (User $user) use ($slug) {
                return $this->userHasPermission($user, $slug);
            });
        }
    }

    public function seedDefaults(): void
    {
        $roleData = [
            ['name' => 'Owner', 'slug' => 'owner', 'description' => 'Full system access', 'is_system' => true],
            ['name' => 'Admin Pusat', 'slug' => 'admin_pusat', 'description' => 'Central admin with full access', 'is_system' => true],
            ['name' => 'Admin Cabang', 'slug' => 'admin', 'description' => 'Branch admin (retail only)', 'is_system' => true],
            ['name' => 'Manager', 'slug' => 'manager', 'description' => 'Branch manager', 'is_system' => true],
            ['name' => 'Supervisor', 'slug' => 'supervisor', 'description' => 'Branch supervisor', 'is_system' => true],
            ['name' => 'Kasir', 'slug' => 'cashier', 'description' => 'Cashier', 'is_system' => true],
            ['name' => 'Gudang', 'slug' => 'warehouse', 'description' => 'Warehouse staff', 'is_system' => true],
        ];

        $permData = [
            ['name' => 'Melihat Produk', 'slug' => 'products.view', 'description' => 'View product list', 'group' => 'Produk'],
            ['name' => 'Membuat Produk', 'slug' => 'products.create', 'description' => 'Create new products', 'group' => 'Produk'],
            ['name' => 'Mengubah Produk', 'slug' => 'products.edit', 'description' => 'Edit existing products', 'group' => 'Produk'],
            ['name' => 'Menghapus Produk', 'slug' => 'products.delete', 'description' => 'Delete products', 'group' => 'Produk'],
            ['name' => 'Melihat Inventaris', 'slug' => 'inventory.view', 'description' => 'View inventory', 'group' => 'Inventaris'],
            ['name' => 'Mengelola Inventaris', 'slug' => 'inventory.manage', 'description' => 'Manage stock', 'group' => 'Inventaris'],
            ['name' => 'Audit Inventaris', 'slug' => 'inventory.audit', 'description' => 'Perform stock audit', 'group' => 'Inventaris'],
            ['name' => 'Melihat Transaksi', 'slug' => 'transactions.view', 'description' => 'View transactions', 'group' => 'Transaksi'],
            ['name' => 'Membuat Transaksi', 'slug' => 'transactions.create', 'description' => 'Create POS transactions', 'group' => 'Transaksi'],
            ['name' => 'Membatalkan Transaksi', 'slug' => 'transactions.cancel', 'description' => 'Cancel transactions', 'group' => 'Transaksi'],
            ['name' => 'Melihat Pelanggan', 'slug' => 'customers.view', 'description' => 'View customers', 'group' => 'Pelanggan'],
            ['name' => 'Mengelola Pelanggan', 'slug' => 'customers.manage', 'description' => 'Create/edit customers', 'group' => 'Pelanggan'],
            ['name' => 'Melihat Grosir', 'slug' => 'wholesale.view', 'description' => 'View wholesale orders', 'group' => 'Grosir'],
            ['name' => 'Mengelola Grosir', 'slug' => 'wholesale.manage', 'description' => 'Manage wholesale', 'group' => 'Grosir'],
            ['name' => 'Melihat Laporan', 'slug' => 'reports.view', 'description' => 'View reports & analytics', 'group' => 'Laporan'],
            ['name' => 'Mengekspor Data', 'slug' => 'reports.export', 'description' => 'Export data', 'group' => 'Laporan'],
            ['name' => 'Melihat Pengeluaran', 'slug' => 'expenses.view', 'description' => 'View expenses', 'group' => 'Keuangan'],
            ['name' => 'Mengelola Pengeluaran', 'slug' => 'expenses.manage', 'description' => 'Create/edit expenses', 'group' => 'Keuangan'],
            ['name' => 'Melihat Kupon', 'slug' => 'coupons.view', 'description' => 'View coupons', 'group' => 'Kupon'],
            ['name' => 'Mengelola Kupon', 'slug' => 'coupons.manage', 'description' => 'Create/edit coupons', 'group' => 'Kupon'],
            ['name' => 'Mengelola Karyawan', 'slug' => 'employees.manage', 'description' => 'Manage employees', 'group' => 'SDM'],
            ['name' => 'Melihat Absensi', 'slug' => 'attendance.view', 'description' => 'View attendance', 'group' => 'SDM'],
            ['name' => 'Mengelola Absensi', 'slug' => 'attendance.manage', 'description' => 'Manage attendance', 'group' => 'SDM'],
            ['name' => 'Melihat Penggajian', 'slug' => 'payroll.view', 'description' => 'View payroll', 'group' => 'SDM'],
            ['name' => 'Mengelola Penggajian', 'slug' => 'payroll.manage', 'description' => 'Manage payroll', 'group' => 'SDM'],
            ['name' => 'Mengelola Toko', 'slug' => 'settings.branches', 'description' => 'Manage branches/stores', 'group' => 'Pengaturan'],
            ['name' => 'Mengelola Pengaturan', 'slug' => 'settings.general', 'description' => 'Manage system settings', 'group' => 'Pengaturan'],
            ['name' => 'Melihat Log Audit', 'slug' => 'audit.view', 'description' => 'View audit logs', 'group' => 'Keamanan'],
            ['name' => 'Mengelola Role', 'slug' => 'roles.manage', 'description' => 'Manage roles & permissions', 'group' => 'Keamanan'],
            ['name' => 'Mengelola Shift', 'slug' => 'shift.manage', 'description' => 'Manage cashier shifts', 'group' => 'Operasional'],
            ['name' => 'Melihat Permintaan Stok', 'slug' => 'stock_requests.view', 'description' => 'View stock requests', 'group' => 'Inventaris'],
            ['name' => 'Membuat Permintaan Stok', 'slug' => 'stock_requests.create', 'description' => 'Create stock requests', 'group' => 'Inventaris'],
            ['name' => 'Menyetujui Permintaan Stok', 'slug' => 'stock_requests.approve', 'description' => 'Approve & prepare stock from pusat', 'group' => 'Inventaris'],
            ['name' => 'Menerima Permintaan Stok', 'slug' => 'stock_requests.receive', 'description' => 'Confirm stock received at branch', 'group' => 'Inventaris'],
            ['name' => 'Melihat Penerimaan Barang', 'slug' => 'goods_receipts.view', 'description' => 'View goods receiving records', 'group' => 'Inventaris'],
            ['name' => 'Mencatat Penerimaan Barang', 'slug' => 'goods_receipts.create', 'description' => 'Record incoming goods to warehouse', 'group' => 'Inventaris'],
        ];

        foreach ($roleData as $r) {
            Role::firstOrCreate(['slug' => $r['slug']], $r);
        }

        foreach ($permData as $p) {
            Permission::firstOrCreate(['slug' => $p['slug']], $p);
        }

        // Assign default permissions per role
        $this->assignDefaultPermissions();
    }

    /**
     * Assign default permissions to each role based on its function.
     * Owner can override individual permissions later via the RBAC UI.
     */
    public function assignDefaultPermissions(): void
    {
        $all = Permission::pluck('id', 'slug');

        // Owner — everything
        $ownerRole = Role::where('slug', 'owner')->first();
        $ownerRole->permissions()->sync($all->values());

        // Admin Pusat — operational full access (sensitive data owner-only)
        $adminPusatSlugs = [
            'products.view', 'products.create', 'products.edit', 'products.delete',
            'inventory.view', 'inventory.manage', 'inventory.audit',
            'transactions.view', 'transactions.create', 'transactions.cancel',
            'customers.view', 'customers.manage',
            'wholesale.view', 'wholesale.manage',
            'reports.view', 'reports.export',
            'expenses.view', 'expenses.manage',
            'coupons.view', 'coupons.manage',
            'attendance.view', 'attendance.manage',
            'shift.manage',
            'stock_requests.view', 'stock_requests.create', 'stock_requests.approve', 'stock_requests.receive',
            'goods_receipts.view', 'goods_receipts.create',
        ];
        $adminPusatPermIds = collect($adminPusatSlugs)->map(fn($s) => $all[$s])->filter()->values();
        $adminPusatRole = Role::where('slug', 'admin_pusat')->first();
        $adminPusatRole->permissions()->sync($adminPusatPermIds);

        // Admin Cabang — POS transactions, stock requests & branch expenses
        // All other management (products, customers, etc.) handled by Owner/Admin Pusat
        $adminSlugs = [
            'products.view',
            'transactions.view', 'transactions.create',
            'stock_requests.view', 'stock_requests.create', 'stock_requests.receive',
            'expenses.view', 'expenses.manage',
        ];
        $adminPermIds = collect($adminSlugs)->map(fn($s) => $all[$s])->filter()->values();
        $adminRole = Role::where('slug', 'admin')->first();
        $adminRole->permissions()->sync($adminPermIds);

        // Manager — branch manager: operational full-access within branch
        $managerSlugs = [
            'products.view', 'products.create', 'products.edit',
            'inventory.view', 'inventory.manage', 'inventory.audit',
            'transactions.view', 'transactions.create',
            'customers.view', 'customers.manage',
            'wholesale.view', 'wholesale.manage',
            'reports.view', 'reports.export',
            'expenses.view', 'expenses.manage',
            'coupons.view', 'coupons.manage',
            'attendance.view', 'attendance.manage',
            'shift.manage',
            'stock_requests.view', 'stock_requests.create', 'stock_requests.approve', 'stock_requests.receive',
            'goods_receipts.view', 'goods_receipts.create',
        ];
        $managerPermIds = collect($managerSlugs)->map(fn($s) => $all[$s])->filter()->values();
        $managerRole = Role::where('slug', 'manager')->first();
        $managerRole->permissions()->sync($managerPermIds);

        // Supervisor — floor supervisor: oversee operations, manage attendance
        $supervisorSlugs = [
            'products.view',
            'inventory.view',
            'transactions.view',
            'customers.view',
            'reports.view',
            'attendance.view', 'attendance.manage',
            'shift.manage',
            'stock_requests.view',
        ];
        $supervisorPermIds = collect($supervisorSlugs)->map(fn($s) => $all[$s])->filter()->values();
        $supervisorRole = Role::where('slug', 'supervisor')->first();
        $supervisorRole->permissions()->sync($supervisorPermIds);

        // Cashier — POS only
        $cashierSlugs = [
            'products.view',
            'transactions.view', 'transactions.create',
            'customers.view',
            'attendance.view',
        ];
        $cashierPermIds = collect($cashierSlugs)->map(fn($s) => $all[$s])->filter()->values();
        $cashierRole = Role::where('slug', 'cashier')->first();
        $cashierRole->permissions()->sync($cashierPermIds);

        // Warehouse / Gudang — incoming goods & inventory management
        $warehouseSlugs = [
            'products.view',
            'inventory.view', 'inventory.manage', 'inventory.audit',
            'attendance.view',
            'stock_requests.view', 'stock_requests.create', 'stock_requests.approve',
            'goods_receipts.view', 'goods_receipts.create',
        ];
        $warehousePermIds = collect($warehouseSlugs)->map(fn($s) => $all[$s])->filter()->values();
        $warehouseRole = Role::where('slug', 'warehouse')->first();
        $warehouseRole->permissions()->sync($warehousePermIds);

        try { Cache::tags(['rbac'])->flush(); } catch (\Exception $e) { Cache::flush(); }
    }
}
