<?php

namespace App\Http\Controllers;

use App\Models\WholesaleProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class WholesaleProductController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('wholesale.view');

        $query = WholesaleProduct::query();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $products = $query->latest()->paginate(15);
        $types = WholesaleProduct::select('type')->distinct()->pluck('type');

        return view('wholesale.products.index', compact('products', 'types'));
    }

    public function create()
    {
        Gate::authorize('wholesale.manage');
        return view('wholesale.products.create');
    }

    public function store(Request $request)
    {
        Gate::authorize('wholesale.manage');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:100',
            'unit' => 'required|string|max:50',
            'pieces_per_unit' => 'required|integer|min:1',
            'price_per_unit' => 'required|numeric|min:0',
            'price_per_ml' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        WholesaleProduct::create($validated);

        return redirect()->route('wholesale.products.index')
            ->with('success', 'Produk grosir berhasil ditambahkan.');
    }

    public function edit(WholesaleProduct $wholesaleProduct)
    {
        Gate::authorize('wholesale.manage');
        return view('wholesale.products.edit', compact('wholesaleProduct'));
    }

    public function update(Request $request, WholesaleProduct $wholesaleProduct)
    {
        Gate::authorize('wholesale.manage');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:100',
            'unit' => 'required|string|max:50',
            'pieces_per_unit' => 'required|integer|min:1',
            'price_per_unit' => 'required|numeric|min:0',
            'price_per_ml' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $wholesaleProduct->update($validated);

        return redirect()->route('wholesale.products.index')
            ->with('success', 'Produk grosir berhasil diperbarui.');
    }

    public function destroy(WholesaleProduct $wholesaleProduct)
    {
        Gate::authorize('wholesale.manage');
        $wholesaleProduct->delete();

        return redirect()->route('wholesale.products.index')
            ->with('success', 'Produk grosir berhasil dihapus.');
    }
}
