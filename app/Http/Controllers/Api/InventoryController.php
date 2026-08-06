<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use Illuminate\Http\JsonResponse;

class InventoryController extends Controller
{
    /**
     * Return low-stock inventory items for the authenticated user's branch.
     */
    public function lowStock(): JsonResponse
    {
        $branchId = auth()->user()->branch_id;

        if (! $branchId) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Branch not assigned to this user.',
            ], 403);
        }

        $items = Inventory::with('product:id,name,sku,selling_price')
            ->whereColumn('current_stock', '<=', 'minimum_stock')
            ->where('branch_id', $branchId)
            ->orderBy('current_stock')
            ->limit(20)
            ->get(['id', 'product_id', 'branch_id', 'current_stock', 'minimum_stock']);

        return response()->json([
            'status' => 'success',
            'data'   => $items,
            'count'  => $items->count(),
        ]);
    }
}
