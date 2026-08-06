<?php

namespace App\Http\Controllers;

use App\Models\Accessory;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class AccessoryController extends Controller
{
    /**
     * Daftar aksesori — ditampilkan sebagai tab kedua di /products
     */
    public function index(Request $request)
    {
        if (! Gate::allows('manage_products') && ! auth()->user()->can('products.view')) {
            abort(403);
        }

        $query = Accessory::with('supplier')->latest();

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($qb) use ($q) {
                $qb->where('name', 'like', "%{$q}%")
                   ->orWhere('sku', 'like', "%{$q}%")
                   ->orWhere('barcode', 'like', "%{$q}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $accessories = $query->paginate(20)->withQueryString();
        $suppliers   = Supplier::orderBy('name')->get();

        return view('accessories.index', compact('accessories', 'suppliers'));
    }

    /**
     * Simpan aksesori baru.
     */
    public function store(Request $request)
    {
        Gate::authorize('manage_products');

        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'sku'             => 'nullable|string|max:100|unique:accessories,sku',
            'barcode'         => 'nullable|string|max:100|unique:accessories,barcode',
            'category'        => 'required|string|in:' . implode(',', array_keys(Accessory::$categories)),
            'brand'           => 'nullable|string|max:100',
            'purchase_price'  => 'required|numeric|min:0',
            'selling_price'   => 'required|numeric|min:0',
            'wholesale_price' => 'nullable|numeric|min:0',
            'current_stock'   => 'required|integer|min:0',
            'minimum_stock'   => 'required|integer|min:0',
            'unit'            => 'required|string|in:pcs,set,buah,lusin,kodi,pak',
            'description'     => 'nullable|string|max:1000',
            'is_active'       => 'boolean',
            'supplier_id'     => 'nullable|exists:suppliers,id',
            'image'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('accessories', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active', true);

        Accessory::create($validated);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Aksesori berhasil ditambahkan.'], 201);
        }

        return redirect()->route('products.index', ['tab' => 'accessories'])
            ->with('success', 'Aksesori berhasil ditambahkan.');
    }

    /**
     * Update aksesori.
     */
    public function update(Request $request, Accessory $accessory)
    {
        Gate::authorize('manage_products');

        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'sku'             => 'nullable|string|max:100|unique:accessories,sku,' . $accessory->id,
            'barcode'         => 'nullable|string|max:100|unique:accessories,barcode,' . $accessory->id,
            'category'        => 'required|string|in:' . implode(',', array_keys(Accessory::$categories)),
            'brand'           => 'nullable|string|max:100',
            'purchase_price'  => 'required|numeric|min:0',
            'selling_price'   => 'required|numeric|min:0',
            'wholesale_price' => 'nullable|numeric|min:0',
            'current_stock'   => 'required|integer|min:0',
            'minimum_stock'   => 'required|integer|min:0',
            'unit'            => 'required|string|in:pcs,set,buah,lusin,kodi,pak',
            'description'     => 'nullable|string|max:1000',
            'is_active'       => 'boolean',
            'supplier_id'     => 'nullable|exists:suppliers,id',
            'image'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($accessory->image) {
                Storage::disk('public')->delete($accessory->image);
            }
            $validated['image'] = $request->file('image')->store('accessories', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active', true);

        $accessory->update($validated);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Aksesori berhasil diperbarui.']);
        }

        return redirect()->route('products.index', ['tab' => 'accessories'])
            ->with('success', 'Aksesori berhasil diperbarui.');
    }

    /**
     * Hapus aksesori.
     */
    public function destroy(Accessory $accessory)
    {
        Gate::authorize('manage_products');

        if ($accessory->image) {
            Storage::disk('public')->delete($accessory->image);
        }

        $accessory->delete();

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Aksesori berhasil dihapus.']);
        }

        return redirect()->route('products.index', ['tab' => 'accessories'])
            ->with('success', 'Aksesori berhasil dihapus.');
    }

    /**
     * API: cari aksesori untuk wholesale create (realtime JSON).
     */
    public function search(Request $request)
    {
        $q = $request->get('q', '');

        $accessories = Accessory::active()
            ->when($q, fn($query) => $query->where('name', 'like', "%{$q}%")
                ->orWhere('sku', 'like', "%{$q}%"))
            ->when($request->filled('category'), fn($query) => $query->where('category', $request->category))
            ->orderBy('name')
            ->limit(50)
            ->get(['id', 'name', 'sku', 'category', 'selling_price', 'wholesale_price', 'current_stock', 'unit', 'image']);

        return response()->json($accessories);
    }
}
