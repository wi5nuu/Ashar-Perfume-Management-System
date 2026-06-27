<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Response;

class CustomerController extends Controller
{
    public function index()
    {
        Gate::authorize('manage_customers');
        $user = auth()->user();
        $query = Customer::query();
        if (!$user->isOwner() && !$user->isAdminPusat()) {
            $query->where('branch_id', $user->branch_id);
        }
        $customers = $query->paginate(10);
        $activeCustomers = (clone $query)->where('is_active', true)->count();
        $wholesaleCustomers = (clone $query)->where('type', 'wholesale')->count();
        $averageSpent = 0;
        return view('customers.index', compact('customers', 'activeCustomers', 'wholesaleCustomers', 'averageSpent'));
    }

    public function create()
    {
        Gate::authorize('manage_customers');
        return view('customers.create');
    }

    public function store(\App\Http\Requests\StoreCustomerRequest $request)
    {
        Gate::authorize('manage_customers');
        $validated = $request->validated();

        // Set default is_active jika tidak ada
        $validated['is_active'] = $validated['is_active'] ?? true;
        $validated['branch_id'] = $validated['branch_id'] ?? auth()->user()->branch_id;

        $customer = Customer::create($validated);

        if (request()->expectsJson()) {
            return response()->json($customer);
        }

        return redirect()->route('customers.index')->with('success', 'Pelanggan berhasil ditambahkan');
    }

    public function show(Customer $customer)
    {
        Gate::authorize('manage_customers');
        $customer->load('transactions');
        if (request()->expectsJson()) {
            return response()->json([
                'customer' => $customer,
                'html' => view('customers.show_details', compact('customer'))->render()
            ]);
        }
        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        Gate::authorize('manage_customers');
        $customer->load('transactions');
        return view('customers.edit', compact('customer'));
    }

    public function update(\App\Http\Requests\StoreCustomerRequest $request, Customer $customer)
    {
        Gate::authorize('manage_customers');
        $validated = $request->validated();

        // Set is_active dari form
        $validated['is_active'] = $request->has('is_active') ? (bool) $request->input('is_active') : false;

        $customer->update($validated);
        return redirect()->route('customers.index')->with('success', 'Pelanggan berhasil diperbarui');
    }

    public function destroy(Customer $customer)
    {
        Gate::authorize('manage_customers');

        // FK safety: deactivate instead of hard-delete when transactions exist
        if ($customer->transactions()->exists() || $customer->wholesaleOrders()->exists()) {
            $customer->update(['is_active' => false]);
            return redirect()->route('customers.index')
                ->with('success', 'Pelanggan dinonaktifkan (memiliki riwayat transaksi).');
        }

        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Pelanggan berhasil dihapus');
    }

    public function search(Request $request)
    {
        Gate::authorize('manage_customers');
        $query = $request->get('q', '');
        $page = $request->get('page', 1);
        $perPage = 30;
        $user = auth()->user();

        $customers = Customer::where('is_active', true)
            ->where(function($q) use ($query) {
                $q->where('name', 'like', '%' . $query . '%')
                  ->orWhere('phone', 'like', '%' . $query . '%')
                  ->orWhere('email', 'like', '%' . $query . '%');
            });
        
        if (!$user->isOwner() && !$user->isAdminPusat()) {
            $customers->where('branch_id', $user->branch_id);
        }
        
        $customers = $customers->orderBy('name')
            ->paginate($perPage, ['*'], 'page', $page);

        $results = $customers->map(function($customer) {
            return [
                'id' => $customer->id,
                'text' => $customer->name,
                'name' => $customer->name,
                'phone' => $customer->phone,
                'email' => $customer->email,
                'type' => $customer->type,
                'points' => $customer->points,
            ];
        });

        return response()->json([
            'items' => $results,
            'total_count' => $customers->total(),
            'current_page' => $customers->currentPage(),
            'last_page' => $customers->lastPage(),
        ]);
    }

    /**
     * Customer statement — all transactions + debt payments, running balance.
     */
    public function export()
    {
        Gate::authorize('manage_customers');
        $user = auth()->user();
        $query = Customer::where('is_active', true);
        if (!$user->isOwner() && !$user->isAdminPusat()) {
            $query->where('branch_id', $user->branch_id);
        }
        $customers = $query->orderBy('name')->get();
        $filename = 'pelanggan-' . date('Y-m-d') . '.csv';
        $handle = fopen('php://temp', 'w');
        fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
        fputcsv($handle, ['No', 'Nama', 'No Telepon', 'Email', 'Tipe', 'Poin', 'Alamat']);
        $no = 1;
        foreach ($customers as $c) {
            fputcsv($handle, [$no++, $c->name, $c->phone, $c->email, $c->type, $c->points ?? 0, $c->address]);
        }
        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);
        return Response::make($content, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function statement(Customer $customer)
    {
        Gate::authorize('manage_customers');

        $transactions = $customer->transactions()
            ->withTrashed()
            ->latest()
            ->get();

        $payments = \App\Models\DebtPayment::whereIn('transaction_id', $transactions->pluck('id'))
            ->orderBy('payment_date', 'asc')
            ->get();

        return view('customers.statement', compact('customer', 'transactions', 'payments'));
    }
}
