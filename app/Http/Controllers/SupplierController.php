<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('manage_suppliers');

        $query = Supplier::withCount(['purchaseOrders', 'supplierPrices']);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('contact_person', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $suppliers = $query->latest()->paginate(20)->withQueryString();

        $totalSuppliers  = Supplier::count();
        $activeSuppliers = Supplier::where('is_active', true)->count();

        return view('suppliers.index', compact('suppliers', 'totalSuppliers', 'activeSuppliers'));
    }

    public function create()
    {
        Gate::authorize('manage_suppliers');
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        Gate::authorize('manage_suppliers');

        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone'          => 'nullable|string|max:30',
            'email'          => 'nullable|email|max:255|unique:suppliers,email',
            'address'        => 'nullable|string|max:1000',
            'is_active'      => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $supplier = Supplier::create($validated);

        Log::info('Supplier created', ['supplier_id' => $supplier->id, 'by' => auth()->id()]);

        return redirect()->route('suppliers.index')
            ->with('success', "Supplier \"{$supplier->name}\" berhasil ditambahkan.");
    }

    public function show(Supplier $supplier)
    {
        Gate::authorize('manage_suppliers');

        $supplier->loadCount(['purchaseOrders', 'supplierPrices']);
        $recentOrders = $supplier->purchaseOrders()->with('items.product')->latest()->limit(5)->get();

        return view('suppliers.show', compact('supplier', 'recentOrders'));
    }

    public function edit(Supplier $supplier)
    {
        Gate::authorize('manage_suppliers');
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        Gate::authorize('manage_suppliers');

        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone'          => 'nullable|string|max:30',
            'email'          => 'nullable|email|max:255|unique:suppliers,email,' . $supplier->id,
            'address'        => 'nullable|string|max:1000',
            'is_active'      => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $supplier->update($validated);

        Log::info('Supplier updated', ['supplier_id' => $supplier->id, 'by' => auth()->id()]);

        return redirect()->route('suppliers.index')
            ->with('success', "Supplier \"{$supplier->name}\" berhasil diperbarui.");
    }

    public function destroy(Supplier $supplier)
    {
        Gate::authorize('manage_suppliers');

        // Prevent deletion if supplier has active purchase orders
        if ($supplier->purchaseOrders()->whereNotIn('status', ['received', 'cancelled'])->exists()) {
            return back()->with('error', 'Supplier tidak dapat dihapus karena memiliki Purchase Order aktif.');
        }

        $name = $supplier->name;
        $supplier->delete();

        Log::info('Supplier deleted', ['name' => $name, 'by' => auth()->id()]);

        return redirect()->route('suppliers.index')
            ->with('success', "Supplier \"{$name}\" berhasil dihapus.");
    }

    /**
     * Quick search suppliers for autocomplete (JSON).
     */
    public function search(Request $request)
    {
        $q = $request->get('q', '');
        $suppliers = Supplier::where('is_active', true)
            ->where('name', 'like', "%{$q}%")
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'name']);

        return response()->json($suppliers);
    }

    /**
     * Quick-create a supplier from inline modal (returns JSON).
     * Requires manage_products permission so aksesori form can use it.
     */
    public function quickStore(Request $request)
    {
        Gate::authorize('manage_products');

        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'phone' => 'nullable|string|max:30',
        ]);

        $supplier = Supplier::firstOrCreate(
            ['name' => $validated['name']],
            ['phone' => $validated['phone'] ?? null, 'is_active' => true]
        );

        Log::info('Supplier quick-created', ['supplier_id' => $supplier->id, 'by' => auth()->id()]);

        return response()->json(['id' => $supplier->id, 'name' => $supplier->name]);
    }
}
