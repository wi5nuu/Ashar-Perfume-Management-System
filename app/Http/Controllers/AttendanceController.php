<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the attendances for the owner.
     */
    public function index(Request $request)
    {
        Gate::authorize('manage_attendance');
        $query = Attendance::with('user');
        $user = auth()->user();

        // Branch filtering: Owner sees all, others see their branch only
        if (!$user->isOwner()) {
            $query->where('branch_id', $user->branch_id);
        }

        // Filter by Month & Year (Calendar)
        if ($request->has('month') && $request->has('year')) {
            $query->whereMonth('date', $request->month)
                  ->whereYear('date', $request->year);
        } elseif ($request->has('date')) {
            $query->whereDate('date', $request->date);
        } else {
            // Default to this month
            $query->whereMonth('date', Carbon::now()->month)
                  ->whereYear('date', Carbon::now()->year);
        }

        $attendances = $query->orderBy('date', 'desc')
                             ->orderBy('time_in', 'desc')
                             ->paginate(30);

        // Fetch all employees for the check-in dropdown - filtered by branch if not owner
        $employeeQuery = \App\Models\User::where('role', '!=', 'owner');
        if (!$user->isOwner()) {
            $employeeQuery->where('branch_id', $user->branch_id);
        }
        $employees = $employeeQuery->get();

        return view('attendances.index', compact('attendances', 'employees'));
    }

    /**
     * Store a newly created attendance from the Manual Popup.
     */
    public function store(Request $request)
    {
        Gate::authorize('manage_attendance');
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'cashier_names' => 'required|string|max:255',
            'role' => 'nullable|string|max:100',
        ], [
            'user_id.required' => 'Karyawan harus dipilih.',
            'cashier_names.required' => 'Nama karyawan wajib diisi.'
        ]);

        $employee = \App\Models\User::findOrFail($request->user_id);

        Attendance::create([
            'user_id' => $request->user_id,
            'branch_id' => $employee->branch_id,
            'cashier_name' => $request->cashier_names,
            'employee_name' => $request->cashier_names,
            'role' => $request->role ?? ($employee->role ?? 'Staff'),
            'status' => $request->status ?? 'present',
            'reason' => $request->reason,
            'date' => Carbon::today(),
            'time_in' => ($request->status ?? 'present') == 'present' ? Carbon::now()->format('H:i:s') : null,
        ]);

        // Remove the flag so the popup doesn't appear again
        session()->forget('needs_attendance');

        if ($request->has('manual')) {
            return back()->with('success', 'Absensi ' . $request->cashier_names . ' sebagai ' . ($request->role ?? 'Staff') . ' berhasil dicatat!');
        }

        // Flash message for the "Welcome" SweetAlert on reload
        return redirect()->route('dashboard')->with([
            'show_welcome_popup' => true,
            'welcome_user_name' => $request->cashier_names
        ]);
    }

    /**
     * Mark an attendance record as checked out.
     */
    public function checkout(Attendance $attendance)
    {
        Gate::authorize('manage_attendance');

        if ($attendance->time_out) {
            return back()->with('error', 'Karyawan sudah melakukan absen pulang.');
        }

        $attendance->update([
            'time_out' => Carbon::now()->format('H:i:s'),
        ]);

        return back()->with('success', 'Absen pulang untuk ' . $attendance->employee_name . ' berhasil dicatat!');
    }
}
