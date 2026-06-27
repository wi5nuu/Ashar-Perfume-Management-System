<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Rules\StrongPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('manage_employees');
        $query = User::with(['branch', 'attendances'])->where('role', '!=', 'owner');

        // Filter: all / login / store
        $filter = $request->get('filter', 'all');
        if ($filter === 'login') {
            $query->where('can_login', true);
        } elseif ($filter === 'store') {
            $query->where('can_login', false);
        }

        // Branch filtering: Owner sees all, others see their branch only
        $user = auth()->user();
        if (!$user->isOwner()) {
            $query->where('branch_id', $user->branch_id);
        }

        $employees = $query->latest()->paginate(10);
        return view('employees.index', compact('employees', 'filter'));
    }

    public function create()
    {
        Gate::authorize('manage_employees');
        return view('employees.create');
    }

    public function store(Request $request)
    {
        Gate::authorize('manage_employees');

        $isStoreEmp = $request->boolean('is_store_employee');

        $rules = [
            'name' => 'required|string|max:255',
            'full_name' => 'nullable|string|max:255',
            'nickname' => 'nullable|string|max:255',
            'nik' => 'nullable|string|max:16',
            'gender' => 'nullable|in:male,female',
            'place_of_birth' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'religion' => 'nullable|in:islam,protestan,katolik,hindu,buddha,khonghucu,others',
            'marital_status' => 'nullable|in:single,married,divorced,widowed',
            'last_education' => 'nullable|string',
            'join_date' => 'nullable|date',
            'employee_id' => 'nullable|string|unique:users',
            'employment_status' => 'nullable|in:permanent,contract,probation,internship',
            'bank_name' => 'nullable|string',
            'bank_account_number' => 'nullable|string',
            'bank_account_holder' => 'nullable|string',
            'npwp' => 'nullable|string',
            'basic_salary' => 'nullable|numeric',
            'phone' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string',
            'emergency_contact_phone' => 'nullable|string',
            'emergency_contact_relation' => 'nullable|string',
        ];

        if ($isStoreEmp) {
            $rules['email'] = 'required|email|unique:users';
            $rules['name'] = 'required|string|max:255';
        } else {
            $rules['email'] = 'required|email|unique:users';
            $rules['role'] = 'required|in:cashier,manager,supervisor,packing,admin,admin_pusat';
            $rules['password'] = ['required', new StrongPassword];
        }

        $validated = $request->validate($rules);

        if ($isStoreEmp) {
            $validated['role'] = 'employee';
            $validated['can_login'] = false;
            $validated['password'] = \Illuminate\Support\Str::random(40); // random password, never used
        } else {
            $validated['can_login'] = true;
            $validated['password'] = bcrypt($validated['password']);
        }

        // Branch assignment
        if (!auth()->user()->isOwner()) {
            $validated['branch_id'] = auth()->user()->branch_id;
        } else {
            $defaultBranch = \App\Models\Branch::first();
            if (!$defaultBranch) {
                return back()->withInput()->with('error', 'Tidak ada cabang yang tersedia. Buat cabang terlebih dahulu.');
            }
            $validated['branch_id'] = $request->input('branch_id') ?? $defaultBranch->id;
        }

        $user = User::create($validated);

        // Assign RBAC role for login users
        if (!$isStoreEmp && isset($validated['role']) && $validated['role'] !== 'employee') {
            $rbacRole = \App\Models\Role::where('slug', $validated['role'])->first();
            if ($rbacRole) {
                $user->roles()->sync([$rbacRole->id]);
            }
        }

        $msg = $isStoreEmp ? 'Karyawan toko berhasil ditambahkan' : 'Karyawan akses login berhasil ditambahkan';
        return redirect()->route('employees.index')->with('success', $msg);
    }

    public function show(User $employee)
    {
        Gate::authorize('manage_employees');
        return view('employees.show', compact('employee'));
    }

    public function edit(User $employee)
    {
        Gate::authorize('manage_employees');
        return view('employees.edit', compact('employee'));
    }

    public function update(Request $request, User $employee)
    {
        Gate::authorize('manage_employees');

        $isStoreEmp = !$employee->can_login;

        $rules = [
            'name' => 'required|string|max:255',
            'full_name' => 'nullable|string|max:255',
            'nickname' => 'nullable|string|max:255',
            'nik' => 'nullable|string|max:16',
            'gender' => 'nullable|in:male,female',
            'place_of_birth' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'religion' => 'nullable|in:islam,protestan,katolik,hindu,buddha,khonghucu,others',
            'marital_status' => 'nullable|in:single,married,divorced,widowed',
            'last_education' => 'nullable|string',
            'origin' => 'nullable|string|max:255',
            'join_date' => 'nullable|date',
            'employee_id' => 'nullable|string|unique:users,employee_id,' . $employee->id,
            'employment_status' => 'nullable|in:permanent,contract,probation,internship',
            'bank_name' => 'nullable|string',
            'bank_account_number' => 'nullable|string',
            'bank_account_holder' => 'nullable|string',
            'npwp' => 'nullable|string',
            'basic_salary' => 'nullable|numeric',
            'email' => 'required|email|unique:users,email,' . $employee->id,
            'phone' => 'nullable|string',
            'living_address' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string',
            'emergency_contact_phone' => 'nullable|string',
            'emergency_contact_relation' => 'nullable|string',
            'skills' => 'nullable|string',
            'is_staying_in_mess' => 'nullable|boolean',
            'password' => ['nullable', new StrongPassword],
        ];

        if (!$isStoreEmp) {
            $rules['role'] = 'required|in:cashier,manager,supervisor,packing,admin,admin_pusat';
        }

        $validated = $request->validate($rules);
        $validated['is_staying_in_mess'] = $request->boolean('is_staying_in_mess');

        if (!auth()->user()->isOwner()) {
            unset($validated['role']);
            if (!$employee->can_login) {
                unset($validated['email']);
            }
        }

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $employee->update($validated);
        return redirect()->route('employees.index')->with('success', 'Karyawan berhasil diperbarui');
    }

    public function destroy(User $employee)
    {
        Gate::authorize('manage_employees');

        if ($employee->isOwner()) {
            return redirect()->route('employees.index')->with('error', 'Akun Owner tidak boleh dihapus.');
        }

        if ($employee->id === auth()->id()) {
            return redirect()->route('employees.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $employee->delete();
        return redirect()->route('employees.index')->with('success', 'Karyawan berhasil dihapus');
    }

    public function attendance(Request $request, User $employee)
    {
        Gate::authorize('manage_employees');

        $validated = $request->validate([
            'check_in' => 'nullable|date_format:Y-m-d H:i:s',
            'check_out' => 'nullable|date_format:Y-m-d H:i:s',
            'date' => 'required|date',
        ]);

        \App\Models\Attendance::create([
            'user_id' => $employee->id,
            'branch_id' => $employee->branch_id,
            'employee_name' => $employee->name,
            'date' => $validated['date'],
            'time_in' => $validated['check_in'] ?? null,
            'time_out' => $validated['check_out'] ?? null,
            'status' => 'present',
        ]);

        return response()->json(['message' => 'Absensi berhasil dicatat']);
    }
}
