<?php

namespace App\Http\Controllers;

use App\Models\ExpenseApproval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ExpenseApprovalController extends Controller
{
    public function index()
    {
        Gate::authorize('manage_expenses');

        return view('expense-approvals.index', [
            'approvals' => ExpenseApproval::with(['requester','approver','expense'])->where('status','pending')->latest()->paginate(20),
        ]);
    }

    public function approve(ExpenseApproval $approval, Request $request)
    {
        Gate::authorize('manage_expenses');

        try {
            $approval->approve(auth()->id(), $request->notes);
            return back()->with('success', 'Pengajuan biaya disetujui');
        } catch (\LogicException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function reject(ExpenseApproval $approval, Request $request)
    {
        Gate::authorize('manage_expenses');

        $request->validate(['notes' => 'required|string|max:1000']);

        try {
            $approval->reject(auth()->id(), $request->notes);
            return back()->with('success', 'Pengajuan biaya ditolak');
        } catch (\LogicException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
