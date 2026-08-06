<?php

namespace App\Http\Controllers;

use App\Models\SalesTarget;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class SalesTargetController extends Controller
{
    public function index()
    {
        Gate::authorize('manage_employees');

        $targets = SalesTarget::with(['branch', 'user'])->latest()->paginate(20);
        return view('sales-targets.index', compact('targets'));
    }

    public function create()
    {
        Gate::authorize('manage_employees');

        return view('sales-targets.create', [
            'branches' => Branch::all(),
            'users' => User::where('is_active', true)->get(),
        ]);
    }

    public function store(Request $request)
    {
        Gate::authorize('manage_employees');
        $validated = $request->validate([
            'branch_id' => 'nullable|exists:branches,id',
            'user_id' => 'nullable|exists:users,id',
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2024',
            'target_amount' => 'required|numeric|min:0',
            'bonus_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        SalesTarget::create($validated);
        return redirect()->route('sales-targets.index')->with('success', 'Target penjualan berhasil dibuat');
    }

    public function show(SalesTarget $target)
    {
        Gate::authorize('manage_employees');

        $target->load(['branch', 'user']);
        $achievement = $target->achievement();
        return view('sales-targets.show', compact('target', 'achievement'));
    }
}
