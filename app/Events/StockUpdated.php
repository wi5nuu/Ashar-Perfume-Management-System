<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StockUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $productId;
    public $productName;
    public $newStock;

    /**
     * Create a new event instance.
     */
    public function __construct($productId, $productName, $newStock)
    {
        $this->productId = $productId;
        $this->productName = $productName;
        $this->newStock = $newStock;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('inventory'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'StockUpdated';
    }
}
