<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;

class ExpiryAlertController extends Controller
{
    /**
     * List products expiring in 90/60/30 days with color coding.
     */
    public function index(Request $request)
    {
        Gate::authorize('manage_inventory');

        $user = auth()->user();
        $query = Inventory::with(['product', 'branch'])
            ->whereNotNull('expiration_date')
            ->where('current_stock', '>', 0);

        if (!$user->isOwner()) {
            $query->where('branch_id', $user->branch_id);
        }

        $items = $query->orderBy('expiration_date', 'asc')->get();

        $now = Carbon::now();
        $critical = collect(); // <= 30 days
        $warning = collect();  // 31-60 days
        $notice = collect();   // 61-90 days

        foreach ($items as $item) {
            $daysLeft = $now->diffInDays($item->expiration_date, false);

            if ($daysLeft <= 0) {
                $item->expiry_status = 'expired';
                $item->days_left = $daysLeft;
                $critical->push($item);
            } elseif ($daysLeft <= 30) {
                $item->expiry_status = 'critical';
                $item->days_left = $daysLeft;
                $critical->push($item);
            } elseif ($daysLeft <= 60) {
                $item->expiry_status = 'warning';
                $item->days_left = $daysLeft;
                $warning->push($item);
            } elseif ($daysLeft <= 90) {
                $item->expiry_status = 'notice';
                $item->days_left = $daysLeft;
                $notice->push($item);
            }
        }

        return view('inventory.expiry-alerts', compact('critical', 'warning', 'notice'));
    }

    /**
     * Dismiss (acknowledge) an expiry alert.
     */
    public function dismiss(Inventory $inventory)
    {
        Gate::authorize('manage_inventory');

        // Mark as acknowledged via notes field
        $inventory->update([
            'notes' => 'acknowledged:' . now()->format('Y-m-d H:i') . ' by:' . auth()->user()->name,
        ]);

        return back()->with('success', 'Alert acknowledged.');
    }
}
