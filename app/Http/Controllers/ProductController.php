<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Inventory;
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
    public function index()
    {
        if (! Gate::allows('manage_products') && ! auth()->user()->can('products.view')) {
            abort(403);
        }
        // Eager loading untuk category dan inventory agar query lebih efisien
        $products = Product::with(['category', 'inventories', 'supplier'])
            ->latest()
            ->paginate(20);
            
        $categories = ProductCategory::all();
        
        return view('products.index', compact('products', 'categories'));
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
            'purchase_price' => 'required|numeric',
            'selling_price' => 'required|numeric',
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
            'purchase_price' => 'required|numeric',
            'selling_price' => 'required|numeric',
            'wholesale_price' => 'nullable|numeric',
            'minimum_stock' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            'size' => 'required',
            'unit' => 'required',
            'brand' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:5000',
            'track_inventory' => 'nullable|boolean',
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
                    $inventory = $product->inventories()
                        ->where('branch_id', auth()->user()->branch_id)
                        ->first();
                    if ($inventory) {
                        $inventory->update([
                            'cost_per_unit' => $request->purchase_price,
                            'minimum_stock' => $request->minimum_stock ?? 10,
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
        $query = $request->input('q');
        $products = Product::with(['inventories' => function ($q) {
                $q->select('product_id', 'current_stock');
            }])
            ->where('name', 'like', "%{$query}%")
            ->orWhere('barcode', 'like', "%{$query}%")
            ->limit(20)
            ->get(['id', 'name', 'barcode', 'selling_price', 'image'])
            ->each(function ($p) {
                $p->stock = (int) ($p->inventories->first()->current_stock ?? 0);
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
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Kode Internal', 'Barcode', 'Nama Produk', 'Kategori', 'Ukuran', 'Stok', 'Harga Beli', 'Harga Jual', 'Harga Grosir'];

        $callback = function() use($products, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($products as $product) {
                fputcsv($file, [
                    $product->internal_id,
                    "'" . $product->barcode,
                    $product->name,
                    $product->category->name ?? '-',
                    $product->size . ' ' . $product->unit,
                    $product->inventory->current_stock ?? 0,
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