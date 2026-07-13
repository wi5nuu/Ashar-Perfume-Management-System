<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\DepositAccount;
use App\Models\DepositTransaction;
use Illuminate\Http\Request;

class CustomerDepositController extends Controller
{
    public function index()
    {
        $accounts = DepositAccount::with('customer')->where('status', 'active')->paginate(20);
        return view('customer-deposits.index', compact('accounts'));
    }

    public function create()
    {
        $customers = Customer::where('is_active', true)->get();
        return view('customer-deposits.create', compact('customers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'initial_deposit' => 'required|numeric|min:0',
        ]);

        $account = DepositAccount::firstOrCreate(
            ['customer_id' => $validated['customer_id']],
            ['balance' => 0, 'status' => 'active']
        );

        if ($validated['initial_deposit'] > 0) {
            $account->deposit($validated['initial_deposit'], 'Setoran awal', auth()->id());
        }

        return redirect()->route('customer-deposits.index')->with('success', 'Rekening deposit berhasil dibuat');
    }

    public function show(DepositAccount $account)
    {
        $account->load(['customer', 'transactions' => fn($q) => $q->latest()->limit(50)]);
        return view('customer-deposits.show', compact('account'));
    }

    public function transaction(DepositAccount $account, Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:deposit,withdrawal',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        try {
            if ($validated['type'] === 'deposit') {
                $account->deposit($validated['amount'], $validated['description'] ?? 'Setoran', auth()->id());
            } else {
                $account->withdraw($validated['amount'], $validated['description'] ?? 'Penarikan', auth()->id());
            }
            return redirect()->route('customer-deposits.show', $account->id)->with('success', 'Transaksi berhasil');
        } catch (\Exception $e) {
            return back()->withErrors(['amount' => $e->getMessage()]);
        }
    }
}
