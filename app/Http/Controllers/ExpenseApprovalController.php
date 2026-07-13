<?php

namespace App\Http\Controllers;

use App\Models\ExpenseApproval;
use Illuminate\Http\Request;

class ExpenseApprovalController extends Controller
{
    public function index()
    {
        return view('expense-approvals.index', [
            'approvals' => ExpenseApproval::with(['requester','approver','expense'])->where('status','pending')->latest()->paginate(20),
        ]);
    }

    public function approve(ExpenseApproval $approval, Request $request)
    {
        $approval->approve(auth()->id(), $request->notes);
        return back()->with('success','Pengajuan biaya disetujui');
    }

    public function reject(ExpenseApproval $approval, Request $request)
    {
        $request->validate(['notes'=>'required|string']);
        $approval->reject(auth()->id(), $request->notes);
        return back()->with('success','Pengajuan biaya ditolak');
    }
}
