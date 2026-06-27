<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class WarehouseController extends Controller
{
    public function index()
    {
        Gate::authorize('manage_inventory');

        $user = auth()->user();

        $query = Warehouse::with('branch')->latest();
        if (!$user->isOwner()) {
            $query->where('branch_id', $user->branch_id);
        }

        $warehouses = $query->paginate(20);

        return view('warehouses.index', compact('warehouses'));
    }

    public function show(Warehouse $warehouse)
    {
        return redirect()->route('warehouses.edit', $warehouse);
    }

    public function create()
    {
        Gate::authorize('manage_inventory');

        $user = auth()->user();
        if ($user->isOwner()) {
            $branches = Branch::where('is_active', true)->get();
        } else {
            $branches = Branch::where('id', $user->branch_id)->get();
        }

        return view('warehouses.create', compact('branches'));
    }

    public function store(Request $request)
    {
        Gate::authorize('manage_inventory');

        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'name'      => 'required|string|max:255',
            'code'      => 'required|string|max:20|unique:warehouses,code',
            'is_active' => 'nullable|boolean',
        ]);

        DB::transaction(function () use ($validated) {
            Warehouse::create([
                'branch_id' => $validated['branch_id'],
                'name'      => $validated['name'],
                'code'      => $validated['code'],
                'is_active' => $validated['is_active'] ?? true,
            ]);
        });

        return redirect()->route('warehouses.index')
            ->with('success', 'Gudang berhasil ditambahkan.');
    }

    public function edit(Warehouse $warehouse)
    {
        Gate::authorize('manage_inventory');

        $user = auth()->user();
        if (!$user->isOwner() && $user->branch_id !== $warehouse->branch_id) {
            abort(403);
        }

        if ($user->isOwner()) {
            $branches = Branch::where('is_active', true)->get();
        } else {
            $branches = Branch::where('id', $user->branch_id)->get();
        }

        return view('warehouses.edit', compact('warehouse', 'branches'));
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        Gate::authorize('manage_inventory');

        $user = auth()->user();
        if (!$user->isOwner() && $user->branch_id !== $warehouse->branch_id) {
            abort(403);
        }

        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'code'      => 'required|string|max:20|unique:warehouses,code,' . $warehouse->id,
            'is_active' => 'nullable|boolean',
        ]);

        DB::transaction(function () use ($warehouse, $validated) {
            $warehouse->update([
                'name'      => $validated['name'],
                'code'      => $validated['code'],
                'is_active' => $validated['is_active'] ?? true,
            ]);
        });

        return redirect()->route('warehouses.index')
            ->with('success', 'Gudang berhasil diperbarui.');
    }

    public function destroy(Warehouse $warehouse)
    {
        Gate::authorize('manage_inventory');

        $user = auth()->user();
        if (!$user->isOwner() && $user->branch_id !== $warehouse->branch_id) {
            abort(403);
        }

        // Nullify inventories referencing this warehouse
        $warehouse->inventories()->update(['warehouse_id' => null]);
        $warehouse->delete();

        return redirect()->route('warehouses.index')
            ->with('success', 'Gudang berhasil dihapus.');
    }
}
