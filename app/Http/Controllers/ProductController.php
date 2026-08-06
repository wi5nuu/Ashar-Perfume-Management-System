<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Inventory;
use App\Models\Accessory;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Picqer\Barcode\BarcodeGeneratorPNG;


class ProductController extends Controller
{
    /**
     * Menampilkan daftar produk.
     */
    public function index(Request $request)
    {
        if (! Gate::allows('manage_products') && ! auth()->user()->can('products.view')) {
            abort(403);
        }

        $products = Product::with(['category', 'inventories', 'supplier'])
            ->latest()
            ->paginate(20);

        $categories = ProductCategory::all();

        // Analytics: total terjual per produk (qty + bonus_quantity)
        $salesData = DB::table('transaction_details')
            ->select(
                'product_id',
                DB::raw('SUM(quantity) as total_qty'),
                DB::raw('SUM(bonus_quantity) as total_bonus'),
                DB::raw('SUM(subtotal) as total_revenue'),
                DB::raw('COUNT(DISTINCT transaction_id) as total_transaksi')
            )
            ->groupBy('product_id')
            ->get()
            ->keyBy('product_id');

        // Analytics: berapa kali jadi free 20ml bonus
        $bonusUsageData = DB::table('transaction_details')
            ->where('bonus_quantity', '>', 0)
            ->select('product_id', DB::raw('SUM(bonus_quantity) as total_free'))
            ->groupBy('product_id')
            ->get()
            ->keyBy('product_id');

        // Aksesori — tab kedua
        $accessoryQuery = Accessory::with('supplier')->latest();
        if ($request->filled('acc_search')) {
            $q = $request->acc_search;
            $accessoryQuery->where(function ($qb) use ($q) {
                $qb->where('name', 'like', "%{$q}%")
                   ->orWhere('sku', 'like', "%{$q}%");
            });
        }
        if ($request->filled('acc_category')) {
            $accessoryQuery->where('category', $request->acc_category);
        }
        $accessories        = $accessoryQuery->paginate(20, ['*'], 'acc_page')->withQueryString();
        $accessoryCategories = Accessory::$categories;
        $suppliers          = Supplier::orderBy('name')->get();

        // Tab aktif (parfum | accessories)
        $activeTab = $request->get('tab', 'parfum');

        return view('products.index', compact(
            'products', 'categories', 'salesData', 'bonusUsageData',
            'accessories', 'accessoryCategories', 'suppliers', 'activeTab'
        ));
    }

    /**
     * Menampilkan form tambah produk.
     */
    public function create()
    {
        Gate::authorize('manage_products');
        $categories = ProductCategory::all();
        return view('products.create', compact('categories'));
    }

    /**
     * Menyimpan produk baru ke database.
     */
    public function store(Request $request)
    {
        Gate::authorize('manage_products');
        // 1. Validasi Input
        $rules = [
            'name' => 'required|string|max:255',
            'barcode' => 'nullable|string|unique:products,barcode',
            'product_category_id' => 'required|exists:product_categories,id',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            'size' => 'required',
            'unit' => 'required',
        ];
        $trackStock = $request->boolean('track_inventory', true);
        if ($trackStock) {
            $rules['initial_stock'] = 'required|integer|min:0';
            $rules['minimum_stock'] = 'nullable|integer|min:0';
        }
        $request->validate($rules);

        // Declare outside closure so catch block can access it for cleanup
        $imagePath = null;

        try {
            DB::transaction(function () use ($request, &$imagePath) {
                // 2. Handle Upload Gambar
                if ($request->hasFile('image')) {
                    $imagePath = $request->file('image')->store('products', 'public');
                }

                // 3. Simpan ke tabel Products
                $productData = [
                    'name' => $request->name,
                    'barcode' => $request->barcode,
                    'product_category_id' => $request->product_category_id,
                    'brand' => $request->brand,
                    'size' => $request->size,
                    'unit' => $request->unit,
                    'purchase_price' => $request->purchase_price,
                    'selling_price' => $request->selling_price,
                    'wholesale_price' => $request->wholesale_price,
                    'image' => $imagePath,
                    'description' => $request->description,
                    'track_inventory' => $trackStock,
                    'is_refill' => $request->boolean('is_refill'),
                    'refill_price_per_ml' => $request->boolean('is_refill') ? $request->refill_price_per_ml : null,
                ];
                if ($trackStock) {
                    $productData['initial_stock'] = $request->initial_stock;
                }
                $product = Product::create($productData);

                // 4. Simpan ke tabel Inventory (hanya jika track_stock aktif)
                if ($trackStock) {
                    Inventory::create([
                        'product_id' => $product->id,
                        'branch_id' => auth()->user()->branch_id,
                        'current_stock' => $request->initial_stock,
                        'minimum_stock' => $request->minimum_stock ?? 10,
                        'cost_per_unit' => $request->purchase_price,
                    ]);
                }
            });

            return redirect()->route('products.index')
                ->with('success', 'Produk dan stok awal berhasil ditambahkan!');

        } catch (\Exception $e) {
            // Clean up uploaded image on failure
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }

            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan detail produk.
     */
    public function show(Product $product)
    {
        if (! Gate::allows('manage_products') && ! auth()->user()->can('products.view')) {
            abort(403);
        }
        $product->load(['category', 'inventories']);
        return view('products.show', compact('product'));
    }

    /**
     * Menampilkan form edit produk.
     */
    public function edit(Product $product)
    {
        Gate::authorize('manage_products');
        $categories = ProductCategory::all();
        $product->load('inventories');
        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Update produk.
     */
    public function update(Request $request, Product $product)
    {
        Gate::authorize('manage_products');
        $request->validate([
            'name' => 'required|string|max:255',
            'product_category_id' => 'required|exists:product_categories,id',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'wholesale_price' => 'nullable|numeric|min:0',
            'minimum_stock' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            'size' => 'required',
            'unit' => 'required',
            'brand' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:5000',
            'track_inventory' => 'nullable|boolean',
            'current_stock_global' => 'nullable|integer|min:0',
        ]);

        try {
            DB::transaction(function () use ($request, $product) {
                // Handle Upload Gambar Baru (opsional)
                if ($request->hasFile('image')) {
                    // Hapus gambar lama
                    if ($product->image) {
                        Storage::disk('public')->delete($product->image);
                    }
                    $imagePath = $request->file('image')->store('products', 'public');
                    $product->image = $imagePath;
                }

                // Update produk
                $product->update([
                    'name' => $request->name,
                    'product_category_id' => $request->product_category_id,
                    'brand' => $request->brand,
                    'size' => $request->size,
                    'unit' => $request->unit,
                    'purchase_price' => $request->purchase_price,
                    'selling_price' => $request->selling_price,
                    'wholesale_price' => $request->wholesale_price,
                    'description' => $request->description,
                    'track_inventory' => $request->boolean('track_inventory', true),
                    'is_refill' => $request->boolean('is_refill'),
                    'refill_price_per_ml' => $request->boolean('is_refill') ? $request->refill_price_per_ml : null,
                ]);

                // Update inventory jika ada dan track_stock aktif
                if ($product->track_inventory) {
                    $branchId = auth()->user()->branch_id;
                    $inventory = $product->inventories()
                        ->when(is_null($branchId), fn($q) => $q->whereNull('branch_id'), fn($q) => $q->where('branch_id', $branchId))
                        ->first();
                    if ($inventory) {
                        $updateData = [
                            'cost_per_unit' => $request->purchase_price,
                            'minimum_stock' => $request->minimum_stock ?? 10,
                        ];
                        // Update current_stock jika diisi dari form
                        if ($request->filled('current_stock_global') && $request->current_stock_global !== null) {
                            $updateData['current_stock'] = (int) $request->current_stock_global;
                            $updateData['stock_in']      = (int) $request->current_stock_global;
                        }
                        $inventory->update($updateData);
                    } else if ($request->filled('current_stock_global')) {
                        // Buat inventory global baru jika belum ada
                        $product->inventories()->create([
                            'branch_id'     => null,
                            'current_stock' => (int) $request->current_stock_global,
                            'stock_in'      => (int) $request->current_stock_global,
                            'stock_out'     => 0,
                            'minimum_stock' => $request->minimum_stock ?? 10,
                            'date_received' => now(),
                        ]);
                    }
                }
            });

            return redirect()->route('products.index')
                ->with('success', 'Produk berhasil diperbarui!');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Hapus produk.
     */
    public function search(Request $request)
    {
        Gate::authorize('manage_products');
        $searchTerm = $request->input('q', '');
        $branchId   = auth()->user()->branch_id;

        $products = Product::with(['inventories' => function ($q) use ($branchId) {
                // Only load inventory for the current branch so stock is accurate
                if ($branchId) {
                    $q->where('branch_id', $branchId);
                } else {
                    $q->whereNull('branch_id');
                }
                $q->select('product_id', 'branch_id', 'current_stock');
            }])
            ->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('barcode', 'like', "%{$searchTerm}%");
            })
            ->limit(20)
            ->get(['id', 'name', 'barcode', 'selling_price', 'image'])
            ->each(function ($p) {
                $p->stock = (int) ($p->inventories->first()?->current_stock ?? 0);
                $p->price = (float) $p->selling_price;
                unset($p->inventories);
            });

        return response()->json($products);
    }

    public function destroy(Product $product)
    {
        Gate::authorize('manage_products');
        try {
            DB::transaction(function () use ($product) {
                // Hapus gambar
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }

                // Hapus semua inventory records untuk produk ini (semua cabang)
                $product->inventories()->delete();

                // Hapus produk
                $product->delete();
            });

            return redirect()->route('products.index')
                ->with('success', 'Produk berhasil dihapus!');

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function bulkDelete(Request $request)
    {
        Gate::authorize('manage_products');
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return response()->json(['error' => 'No products selected'], 400);
        }
        Product::whereIn('id', $ids)->delete();
        return response()->json(['success' => true]);
    }

    /**
     * Print barcode produk.
     */
    public function printBarcode(Product $product)
    {
        if (! Gate::allows('manage_products') && ! auth()->user()->can('products.view')) {
            abort(403);
        }
        return view('products.barcode', compact('product'));
    }

    public function renderBarcode(Product $product)
    {
        if (! Gate::allows('manage_products') && ! auth()->user()->can('products.view')) {
            abort(403);
        }
        if (!$product->barcode) {
            abort(404, 'Produk tidak memiliki barcode.');
        }
        $generator = new BarcodeGeneratorPNG();
        $barcode = $generator->getBarcode($product->barcode, $generator::TYPE_CODE_128, 2, 60);
        return response($barcode, 200, ['Content-Type' => 'image/png']);
    }

    /**
     * Export daftar produk ke PDF.
     */
    public function exportPDF(Request $request)
    {
        Gate::authorize('manage_products');
        $query = Product::with(['category', 'inventories'])->latest();
        
        if ($request->has('ids')) {
            $ids = explode(',', $request->ids);
            $query->whereIn('id', $ids);
        }

        $products = $query->get();
        $pdf = Pdf::loadView('products.export-pdf', compact('products'));
        
        return $pdf->download('daftar-produk-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Export daftar produk ke CSV.
     */
    public function exportCSV(Request $request)
    {
        Gate::authorize('manage_products');
        $filename = 'daftar-produk-' . date('Y-m-d') . '.csv';
        $query = Product::with(['category', 'inventories'])->latest();

        if ($request->has('ids')) {
            $ids = explode(',', $request->ids);
            $query->whereIn('id', $ids);
        }

        $products = $query->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control'       => 'no-cache, no-store, must-revalidate',
            'Pragma'              => 'no-cache',
            'Expires'             => '0',
        ];

        $columns = ['Kode Internal', 'Barcode', 'Nama Produk', 'Kategori', 'Ukuran', 'Stok', 'Harga Beli', 'Harga Jual', 'Harga Grosir'];

        $callback = function() use($products, $columns) {
            $file = fopen('php://output', 'w');
            // UTF-8 BOM so Excel opens the file with correct encoding
            fputs($file, "\xEF\xBB\xBF");
            fputcsv($file, $columns);
            $safe = fn($v) => is_string($v) && strlen($v) > 0 && in_array($v[0], ['=', '+', '-', '@']) ? "'" . $v : $v;

            $branchId = auth()->user()->branch_id;

            foreach ($products as $product) {
                $stock = 0;
                if ($branchId) {
                    $inv = $product->inventories->firstWhere('branch_id', $branchId);
                    $stock = $inv?->current_stock ?? 0;
                } else {
                    $stock = $product->inventories->sum('current_stock');
                }
                fputcsv($file, [
                    $product->internal_id,
                    "'" . $product->barcode,
                    $safe($product->name),
                    $safe($product->category->name ?? '-'),
                    $safe($product->size . ' ' . $product->unit),
                    $stock,
                    $product->purchase_price,
                    $product->selling_price,
                    $product->wholesale_price,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}