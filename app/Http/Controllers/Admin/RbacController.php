<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Services\Security\RbacService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class RbacController extends Controller
{
    public function __construct(protected RbacService $rbac)
    {
        $this->middleware('can:roles.manage');
    }

    public function index()
    {
        $roles = Role::withCount('users', 'permissions')->get();

        $adminPusatCount = User::whereHas('roles', fn($q) => $q->where('slug', 'admin_pusat'))->count();
        $adminCabangCount = User::whereHas('roles', fn($q) => $q->where('slug', 'admin'))->count();
        $branches = Branch::where('is_active', true)->count();

        return view('admin.rbac.index', compact('roles', 'adminPusatCount', 'adminCabangCount', 'branches'));
    }

    public function show(Role $role)
    {
        $role->load('permissions');
        $permissions = Permission::getByGroup();
        return view('admin.rbac.show', compact('role', 'permissions'));
    }

    public function syncPermissions(Request $request, Role $role)
    {
        $request->validate(['permissions' => 'array']);
        $this->rbac->syncRolePermissions($role, $request->input('permissions', []));
        return redirect()->route('admin.rbac.show', $role)
            ->with('success', 'Izin role berhasil diperbarui.');
    }

    public function users(Role $role, Request $request)
    {
        $query = $role->users()->with('branch');
        if ($role->slug === 'admin') {
            if ($request->branch === 'null') {
                $query->whereNull('branch_id');
            } elseif ($request->branch === 'notnull') {
                $query->whereNotNull('branch_id');
            }
        }
        $users = $query->paginate(20);
        return view('admin.rbac.users', compact('role', 'users'));
    }

    public function assignUser(Request $request, Role $role)
    {
        $request->validate(['user_id' => 'required|exists:users,id']);
        $user = User::findOrFail($request->user_id);
        $user->roles()->syncWithoutDetaching([$role->id]);
        $this->rbac->invalidateUserCache($user);
        return redirect()->route('admin.rbac.users', $role)
            ->with('success', "Pengguna {$user->name} ditambahkan ke role {$role->name}.");
    }

    public function removeUser(Role $role, User $user)
    {
        $user->roles()->detach($role->id);
        $this->rbac->invalidateUserCache($user);
        return redirect()->route('admin.rbac.users', $role)
            ->with('success', "Pengguna {$user->name} dihapus dari role {$role->name}.");
    }

    public function userPermissions(User $user)
    {
        $user->load('permissions', 'roles.permissions');
        $allPermissions = Permission::getByGroup();
        $userPermIds = $user->permissions->pluck('id')->toArray();
        return view('admin.rbac.user-permissions', compact('user', 'allPermissions', 'userPermIds'));
    }

    public function syncUserPermissions(Request $request, User $user)
    {
        $request->validate(['permissions' => 'array']);
        $this->rbac->syncUserPermissions($user, $request->input('permissions', []));
        return redirect()->route('admin.rbac.user-permissions', $user)
            ->with('success', 'Izin khusus pengguna berhasil diperbarui.');
    }
}
