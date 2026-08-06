<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LowStockAlert implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $productId;
    public $productName;
    public $currentStock;
    public $minimumStock;
    public $branchId;

    public function __construct($productId, $productName, $currentStock, $minimumStock, $branchId = null)
    {
        $this->productId    = $productId;
        $this->productName  = $productName;
        $this->currentStock = $currentStock;
        $this->minimumStock = $minimumStock;
        $this->branchId     = $branchId;
    }

    public function broadcastOn(): array
    {
        // Broadcast on the branch-scoped channel so only relevant staff receive
        // the alert. Fall back to a wildcard branch 0 if branch_id is unknown.
        $branch = $this->branchId ?? 0;
        return [new PrivateChannel("notifications.{$branch}")];
    }

    public function broadcastAs(): string
    {
        return 'LowStockAlert';
    }

    public function broadcastWith(): array
    {
        return [
            'type'     => 'low_stock',
            'title'    => 'Stok Rendah!',
            'message'  => "{$this->productName} tersisa {$this->currentStock} (min: {$this->minimumStock})",
            'severity' => $this->currentStock <= 0 ? 'critical' : 'warning',
        ];
    }
}
