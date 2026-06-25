<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Product;
use App\Models\Inventory;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SmartInsightService
{
    /**
     * Generate all smart insights for the dashboard.
     */
    public function generateInsights()
    {
        $insights = [];

        // 1. Top Selling Product (Last 7 Days)
        if ($topProduct = $this->getTopSellingProduct()) {
            $insights[] = [
                'type' => 'trend',
                'title' => 'Produk Terlaris Minggu Ini',
                'text' => "Aroma {$topProduct->product_name} sedang naik daun dengan {$topProduct->total_qty} unit terjual.",
                'icon' => 'fa-fire',
                'color' => 'text-danger'
            ];
        }

        // 2. Low Stock Depletion Warning
        if ($lowStock = $this->getLowStockDepletion()) {
            $insights[] = [
                'type' => 'alert',
                'title' => 'Peringatan Stok Rendah',
                'text' => "Stok {$lowStock->name} tinggal {$lowStock->stock} {$lowStock->unit}, diprediksi habis dalam waktu dekat.",
                'icon' => 'fa-exclamation-triangle',
                'color' => 'text-warning'
            ];
        }

        // 3. Sales Trend
        $growth = $this->getSalesGrowth();
        if ($growth != 0) {
            $status = $growth > 0 ? 'meningkat' : 'menurun';
            $arrow = $growth > 0 ? 'fa-arrow-up' : 'fa-arrow-down';
            $color = $growth > 0 ? 'text-success' : 'text-danger';
            $absGrowth = abs($growth);
            
            $insights[] = [
                'type' => 'finance',
                'title' => 'Tren Penjualan',
                'text' => "Penjualan Anda {$status} {$absGrowth}% dibandingkan periode sebelumnya.",
                'icon' => $arrow,
                'color' => $color
            ];
        }

        // 4. Peak Hours Today
        if ($peakHour = $this->getPeakHourToday()) {
            $insights[] = [
                'type' => 'operational',
                'title' => 'Jam Sibuk Hari Ini',
                'text' => "Toko paling ramai pada jam {$peakHour}:00. Pastikan kasir siap melayani.",
                'icon' => 'fa-clock',
                'color' => 'text-info'
            ];
        }

        return $insights;
    }

    protected function getTopSellingProduct()
    {
        return TransactionDetail::join('products', 'transaction_details.product_id', '=', 'products.id')
            ->select('products.name as product_name', DB::raw('SUM(transaction_details.quantity) as total_qty'))
            ->where('transaction_details.created_at', '>=', Carbon::now()->subDays(7))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_qty')
            ->first();
    }

    protected function getLowStockDepletion()
    {
        // Simple logic: products with current_stock < 10
        return Product::join('inventories', 'products.id', '=', 'inventories.product_id')
            ->select('products.name', 'inventories.current_stock as stock', 'products.unit')
            ->where('inventories.current_stock', '<', 10)
            ->where('inventories.current_stock', '>', 0)
            ->first();
    }

    protected function getSalesGrowth()
    {
        $currentSales = Transaction::whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()])
            ->sum('total_amount');
            
        $previousSales = Transaction::whereBetween('created_at', [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()])
            ->sum('total_amount');

        if ($previousSales == 0) return 0;

        return round((($currentSales - $previousSales) / $previousSales) * 100, 1);
    }

    protected function getPeakHourToday()
    {
        return Transaction::select(DB::raw('HOUR(created_at) as hour'))
            ->whereDate('created_at', Carbon::today())
            ->groupBy('hour')
            ->orderByDesc(DB::raw('COUNT(*)'))
            ->pluck('hour')
            ->first();
    }
    /**
     * Get insights filtered by user role.
     */
    public function getRoleSpecificInsights(string $role)
    {
        $allInsights = $this->generateInsights();
        
        // Roles that CAN see financial growth and sensitive trends
        $financeRoles = ['owner', 'admin'];

        if (in_array($role, $financeRoles)) {
            return $allInsights;
        }

        // Filter out financial insights for other roles
        return array_filter($allInsights, function($insight) {
            return $insight['type'] !== 'finance';
        });
    }
}
