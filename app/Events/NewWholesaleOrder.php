<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewWholesaleOrder implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $orderId;
    public $invoiceNumber;
    public $customerName;
    public $totalAmount;

    public function __construct($orderId, $invoiceNumber, $customerName, $totalAmount)
    {
        $this->orderId = $orderId;
        $this->invoiceNumber = $invoiceNumber;
        $this->customerName = $customerName;
        $this->totalAmount = $totalAmount;
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('notifications')];
    }

    public function broadcastAs(): string
    {
        return 'NewWholesaleOrder';
    }

    public function broadcastWith(): array
    {
        return [
            'type'    => 'new_wholesale',
            'title'   => 'Order Grosir Baru',
            'message' => "Order {$this->invoiceNumber} dari {$this->customerName} — Rp " . number_format($this->totalAmount, 0, ',', '.'),
        ];
    }
}
