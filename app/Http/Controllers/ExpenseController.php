<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Http\Requests\StoreExpenseRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    /**
     * Display a listing of expenses with branch scoping.
     */
    public function index()
    {
        Gate::authorize('expenses.view');

        $user = auth()->user();
        $query = Expense::with(['category', 'user']);

        if (!$user->isOwner()) {
            $query->where('branch_id', $user->branch_id);
        }

        $expenses      = $query->latest()->paginate(20);
        $totalExpenses = (clone $query)->sum('amount');

        return view('expenses.index', compact('expenses', 'totalExpenses'));
    }

    /**
     * Show the form for creating a new expense.
     */
    public function create()
    {
        Gate::authorize('expenses.manage');

        $categories = ExpenseCategory::all();
        if ($categories->isEmpty()) {
            ExpenseCategory::create(['name' => 'Store Operations']);
            ExpenseCategory::create(['name' => 'Employee Salary']);
            ExpenseCategory::create(['name' => 'Building Rent']);
            ExpenseCategory::create(['name' => 'Other']);
            $categories = ExpenseCategory::all();
        }

        return view('expenses.create', compact('categories'));
    }

    /**
     * Store a new expense with branch scoping.
     */
    public function store(StoreExpenseRequest $request)
    {
        Gate::authorize('expenses.manage');
        $validated = $request->validated();
        $user      = Auth::user();

        $validated['user_id']   = $user->id;
        $validated['branch_id'] = $user->branch_id;

        if ($request->hasFile('proof_image')) {
            $validated['proof_image'] = $request->file('proof_image')->store('expenses', 'public');
        }

        Expense::create($validated);

        Log::info('Expense created', [
            'user_id'   => $user->id,
            'branch_id' => $user->branch_id,
            'amount'    => $validated['amount'],
        ]);

        return redirect()
            ->route('expenses.index')
            ->with('success', 'Expense recorded successfully.');
    }

    /**
     * Display a single expense.
     */
    public function show(Expense $expense)
    {
        Gate::authorize('view', $expense);

        $expense->load(['category', 'user', 'branch']);

        return view('expenses.show', compact('expense'));
    }

    /**
     * Show the form for editing an expense.
     */
    public function edit(Expense $expense)
    {
        Gate::authorize('update', $expense);

        $categories = ExpenseCategory::all();

        return view('expenses.edit', compact('expense', 'categories'));
    }

    /**
     * Update an expense with branch scoping.
     */
    public function update(StoreExpenseRequest $request, Expense $expense)
    {
        Gate::authorize('update', $expense);

        $validated = $request->validated();

        if ($request->hasFile('proof_image')) {
            if ($expense->proof_image) {
                Storage::disk('public')->delete($expense->proof_image);
            }
            $validated['proof_image'] = $request->file('proof_image')->store('expenses', 'public');
        }

        $expense->update($validated);

        Log::info('Expense updated', [
            'id'      => $expense->id,
            'user_id' => auth()->id(),
        ]);

        return redirect()
            ->route('expenses.index')
            ->with('success', 'Expense updated successfully.');
    }

    /**
     * Soft-delete an expense with branch authorization.
     *
     * BEFORE (IDOR VULNERABLE):
     *   No branch ownership check — any admin/manager could delete any expense.
     *
     * AFTER (SECURE):
     *   Gate::authorize('delete', $expense) checks ExpensePolicy which
     *   enforces branch_id match. Soft delete preserves audit trail.
     */
    public function destroy(Expense $expense)
    {
        Gate::authorize('delete', $expense);

        if ($expense->proof_image) {
            Storage::disk('public')->delete($expense->proof_image);
        }

        Log::info('Expense soft-deleted', [
            'id'      => $expense->id,
            'user_id' => auth()->id(),
        ]);

        $expense->delete(); // SoftDeletes trait handles soft delete

        return redirect()
            ->route('expenses.index')
            ->with('success', 'Expense deleted successfully.');
    }
}
