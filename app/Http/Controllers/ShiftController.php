<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\Transaction;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('manage_transactions');
        if (Auth::user()->isOwner()) {
            abort(403, 'Owner tidak dapat membuka atau mengelola shift.');
        }
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();
        $shifts = Shift::with('user')->latest()->paginate(20);
        $activeShift = Shift::with('user')->where('user_id', $authUser->id)->where('status', 'open')->first();
        
        return view('shifts.index', compact('shifts', 'activeShift'));
    }

    /**
     * Show the form to open a new shift (redirects to shifts index).
     */
    public function create()
    {
        if (Auth::user()->isOwner()) {
            abort(403, 'Owner tidak dapat membuka shift.');
        }
        return redirect()->route('shifts.index')
            ->with('info', 'Gunakan form di halaman Shift untuk membuka shift baru.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Shift $shift)
    {
        Gate::authorize('manage_transactions');
        if (Auth::user()->isOwner()) {
            abort(403);
        }
        $shift->load(['user', 'reviewer']);
        return view('shifts.show', compact('shift'));
    }

    /**
     * Store a newly created shift (Open Shift).
     */
    public function store(Request $request)
    {
        Gate::authorize('manage_transactions');
        /** @var User $user */
        $user = Auth::user();
        if ($user->isOwner()) {
            abort(403, 'Owner tidak dapat membuka shift.');
        }

        $request->validate([
            'initial_cash' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();
            $activeShift = Shift::where('user_id', $user->id)->where('status', 'open')->lockForUpdate()->first();
            if ($activeShift) {
                DB::rollBack();
                return back()->with('error', 'Anda masih memiliki shift yang terbuka. Tutup shift tersebut terlebih dahulu.');
            }

            Shift::create([
                'user_id' => $user->id,
                'start_time' => now(),
                'initial_cash' => $request->initial_cash,
                'status' => 'open',
            ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to open shift', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            return back()->with('error', 'Gagal membuka shift. Silakan coba lagi.');
        }

        return redirect()->route('dashboard')->with('success', 'Shift berhasil dibuka! Selamat bekerja.');
    }

    /**
     * Update the shift (Close Shift).
     */
    public function update(Request $request, Shift $shift)
    {
        Gate::authorize('manage_transactions');

        $user = Auth::user();
        if ($user->isOwner()) {
            abort(403, 'Owner tidak dapat menutup shift.');
        }
        if (!$user->isOwner() && !$user->isAdminPusat() && !$user->isManager() && $shift->user_id !== $user->id) {
            abort(403, 'Anda hanya dapat menutup shift milik sendiri.');
        }

        $request->validate([
            'actual_cash' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'closing_photo' => 'required|image|mimes:jpeg,png,jpg|max:1024' // 1MB Max
        ], [
            'closing_photo.required' => 'FOTO BUKU CATATAN WAJIB DIUPLOAD UNTUK MENUTUP KASIR.',
            'closing_photo.image' => 'File harus berupa gambar.',
            'closing_photo.max' => 'UKURAN FOTO MAKSIMAL 1 MB. Jika ukuran terlalu besar, sistem tidak dapat memprosesnya untuk menjaga memori jangka panjang.',
        ]);

        if ($shift->status === 'closed') {
            return back()->with('error', 'Shift ini sudah ditutup.');
        }

        // Calculate expected cash
        // Expected = Initial + Cash Sales - Cash Expenses
        
        $cashSales = Transaction::where('user_id', $shift->user_id)
            ->where('payment_method', 'cash')
            ->whereBetween('created_at', [$shift->start_time, now()])
            ->selectRaw('SUM(paid_amount - change_amount) as net_cash')
            ->value('net_cash') ?? 0;

        $cashExpenses = Expense::where('user_id', $shift->user_id)
            ->whereBetween('date', [$shift->start_time?->format('Y-m-d H:i:s') ?? now()->subDay()->format('Y-m-d H:i:s'), now()->format('Y-m-d H:i:s')])
            ->sum('amount') ?? 0;

        $expectedCash = $shift->initial_cash + $cashSales - $cashExpenses;
        $discrepancy = $request->actual_cash - $expectedCash;

        // Handle File Upload
        $photoPath = null;
        /** @var User $closer */
        $closer = Auth::user();
        if ($request->hasFile('closing_photo')) {
            $file = $request->file('closing_photo');
            $filename = 'closing_' . date('Ymd_His') . '_' . $closer->id . '.' . $file->getClientOriginalExtension();
            $photoPath = $file->storeAs('shifts/closing_photos', $filename, 'public');
        }

        $shift->update([
            'end_time' => now(),
            'expected_cash' => $expectedCash,
            'actual_cash' => $request->actual_cash,
            'discrepancy' => $discrepancy,
            'status' => 'closed',
            'notes' => $request->notes,
            'closing_photo_path' => $photoPath,
            'photo_status' => 'pending',
        ]);

        return redirect()->route('shifts.index')->with('success', 'Shift berhasil ditutup beserta foto buktinya. Laporan selisih: Rp ' . number_format($discrepancy, 0, ',', '.'));
    }

    /**
     * Get active shift data for the current user.
     */
    public static function getActiveShift()
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();
        return Shift::where('user_id', $currentUser->id)->where('status', 'open')->first();
    }

    /**
     * Show edit form (redirected as shifts are managed through open/close).
     */
    public function edit(Shift $shift)
    {
        return redirect()->route('shifts.show', $shift)
            ->with('info', 'Edit shift tidak tersedia.');
    }

    /**
     * Delete a shift.
     */
    public function destroy(Shift $shift)
    {
        if (Auth::user()->isOwner()) {
            abort(403);
        }
        Gate::authorize('manage_employees');

        $shift->delete();

        return redirect()->route('shifts.index')->with('success', 'Shift berhasil dihapus.');
    }

    /**
     * Admin reviews the uploaded closing photo.
     */
    public function reviewPhoto(Request $request, Shift $shift)
    {
        if (Auth::user()->isOwner()) {
            abort(403);
        }
        Gate::authorize('manage_employees');
        /** @var User $reviewer */
        $reviewer = Auth::user();

        $request->validate([
            'action' => 'required|in:approve,reject'
        ]);

        $status = $request->action === 'approve' ? 'approved' : 'rejected';

        $shift->update([
            'photo_status' => $status,
            'photo_reviewed_by' => $reviewer->id
        ]);

        $msg = $status === 'approved' ? 'Foto bukti telah di-ACC.' : 'Foto bukti DITOLAK.';
        return back()->with('success', $msg);
    }
}
