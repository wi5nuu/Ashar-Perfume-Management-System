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

    public function __construct($productId, $productName, $currentStock, $minimumStock)
    {
        $this->productId = $productId;
        $this->productName = $productName;
        $this->currentStock = $currentStock;
        $this->minimumStock = $minimumStock;
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('notifications')];
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
