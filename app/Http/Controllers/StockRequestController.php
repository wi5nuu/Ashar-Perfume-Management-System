<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Product;
use App\Models\StockRequest;
use App\Models\StockRequestItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class StockRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:stock_requests.view')->only(['index', 'show']);
        $this->middleware('can:stock_requests.create')->only(['create', 'store']);
        $this->middleware('can:stock_requests.approve')->only(['approve', 'prepare', 'ship']);
        $this->middleware('can:stock_requests.receive')->only(['receive']);
    }

    public function index()
    {
        $user = auth()->user();
        $query = StockRequest::with(['branch', 'requester', 'items.product'])->latest();

        if (!$user->isOwner()) {
            if ($user->isAdminPusat() || $user->isAdminCabang()) {
                if ($user->branch_id) {
                    $query->where('branch_id', $user->branch_id);
                }
            } else {
                $query->where('branch_id', $user->branch_id);
            }
        }

        $requests = $query->paginate(15);
        $stats = [
            'pending' => StockRequest::where('status', 'pending')->count(),
            'shipped' => StockRequest::where('status', 'shipped')->count(),
            'received' => StockRequest::where('status', 'received')->count(),
        ];

        return view('stock-requests.index', compact('requests', 'stats'));
    }

    public function create()
    {
        $user = auth()->user();
        $products = Product::with('inventories')->orderBy('name')->get();

        if ($user->isAdminPusat() || $user->isOwner()) {
            $branches = Branch::where('is_active', true)->where('id', '!=', function ($q) {
                $q->select('id')->from('branches')->where('code', 'PST-01')->limit(1);
            })->get();
        } else {
            $branches = Branch::where('id', $user->branch_id)->get();
        }

        return view('stock-requests.create', compact('products', 'branches'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ], [
            'items.required' => 'Minimal 1 produk harus ditambahkan.',
            'items.*.quantity.min' => 'Jumlah minimal 1.',
        ]);

        $user = auth()->user();
        if (!$user->isOwner() && !$user->isAdminPusat()) {
            $data['branch_id'] = $user->branch_id;
        }

        $request = DB::transaction(function () use ($data, $user) {
            $sr = StockRequest::create([
                'branch_id' => $data['branch_id'],
                'requested_by' => $user->id,
                'notes' => $data['notes'] ?? null,
                'status' => 'pending',
            ]);

            foreach ($data['items'] as $item) {
                StockRequestItem::create([
                    'stock_request_id' => $sr->id,
                    'product_id' => $item['product_id'],
                    'quantity_requested' => $item['quantity'],
                ]);
            }

            return $sr;
        });

        return redirect()->route('stock-requests.show', $request)
            ->with('success', "Permintaan stok {$request->request_number} berhasil diajukan.");
    }

    public function show(StockRequest $stockRequest)
    {
        $stockRequest->load(['branch', 'requester', 'approver', 'items.product']);
        return view('stock-requests.show', compact('stockRequest'));
    }

    public function approve(StockRequest $stockRequest, Request $request)
    {
        $request->validate(['notes' => 'nullable|string|max:1000']);

        $stockRequest->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'notes' => $request->notes ?? $stockRequest->notes,
        ]);

        return redirect()->route('stock-requests.show', $stockRequest)
            ->with('success', "{$stockRequest->request_number} telah disetujui.");
    }

    public function prepare(StockRequest $stockRequest, Request $request)
    {
        $data = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:stock_request_items,id',
            'items.*.quantity_prepared' => 'required|integer|min:0',
            'delivery_method' => 'required|string|max:255',
            'delivery_date' => 'required|date',
            'receipt_notes' => 'nullable|string|max:1000',
        ]);

        DB::transaction(function () use ($stockRequest, $data) {
            foreach ($data['items'] as $item) {
                StockRequestItem::where('id', $item['id'])
                    ->where('stock_request_id', $stockRequest->id)
                    ->update(['quantity_prepared' => $item['quantity_prepared']]);
            }

            $stockRequest->update([
                'status' => 'preparing',
                'delivery_method' => $data['delivery_method'],
                'delivery_date' => $data['delivery_date'],
                'receipt_notes' => $data['receipt_notes'] ?? null,
            ]);
        });

        return redirect()->route('stock-requests.show', $stockRequest)
            ->with('success', 'Stok sedang disiapkan.');
    }

    public function ship(StockRequest $stockRequest)
    {
        DB::transaction(function () use ($stockRequest) {
            $stockRequest->update(['status' => 'shipped']);

            foreach ($stockRequest->items as $item) {
                $inventory = \App\Models\Inventory::where('product_id', $item->product_id)
                    ->whereNull('branch_id')
                    ->lockForUpdate()
                    ->first();
                if ($inventory && $item->quantity_prepared > 0) {
                    $inventory->decrement('current_stock', $item->quantity_prepared);
                }
            }
        });

        return redirect()->route('stock-requests.show', $stockRequest)
            ->with('success', "{$stockRequest->request_number} telah dikirim.");
    }

    public function receive(StockRequest $stockRequest, Request $request)
    {
        $data = $request->validate([
            'received_date' => 'required|date',
            'items' => 'required|array',
            'items.*.id' => 'required|exists:stock_request_items,id',
            'items.*.quantity_received' => 'required|integer|min:0',
        ]);

        DB::transaction(function () use ($stockRequest, $data) {
            foreach ($data['items'] as $item) {
                StockRequestItem::where('id', $item['id'])
                    ->where('stock_request_id', $stockRequest->id)
                    ->update(['quantity_received' => $item['quantity_received']]);

                $itemModel = StockRequestItem::find($item['id']);
                if ($itemModel && $item['quantity_received'] > 0) {
                    \App\Models\Inventory::firstOrCreate(
                        ['product_id' => $itemModel->product_id, 'branch_id' => $stockRequest->branch_id],
                        ['stock' => 0, 'min_stock' => 0]
                    )->increment('stock', $item['quantity_received']);
                }
            }

            $stockRequest->update([
                'status' => 'received',
                'received_date' => $data['received_date'],
            ]);
        });

        return redirect()->route('stock-requests.show', $stockRequest)
            ->with('success', "{$stockRequest->request_number} telah diterima.");
    }

    public function cancel(StockRequest $stockRequest)
    {
        if (!in_array($stockRequest->status, ['pending', 'approved'])) {
            return back()->with('error', 'Hanya permintaan pending/approved yang bisa dibatalkan.');
        }

        $stockRequest->update(['status' => 'cancelled']);
        return redirect()->route('stock-requests.show', $stockRequest)
            ->with('success', "{$stockRequest->request_number} dibatalkan.");
    }
}
