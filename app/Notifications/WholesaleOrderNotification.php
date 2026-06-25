<?php

namespace App\Notifications;

use App\Models\WholesaleOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class WholesaleOrderNotification extends Notification
{
    use Queueable;

    public WholesaleOrder $order;
    public string $action;

    public function __construct(WholesaleOrder $order, string $action)
    {
        $this->order = $order;
        $this->action = $action;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $statusLabels = [
            'pending' => 'Pesanan Dibuat',
            'reviewed' => 'Pesanan Dikonfirmasi',
            'on_progress' => 'Pesanan Diproses',
            'packed' => 'Pesanan Di-packing',
            'shipped' => 'Pesanan Dikirim',
            'delivered' => 'Pesanan Diterima',
            'completed' => 'Pesanan Selesai',
            'cancelled' => 'Pesanan Dibatalkan',
        ];

        return [
            'title' => $statusLabels[$this->action] ?? 'Update Pesanan',
            'message' => "Pesanan {$this->order->invoice_number} telah {$statusLabels[$this->action]}.",
            'invoice_number' => $this->order->invoice_number,
            'order_id' => $this->order->id,
            'status' => $this->action,
            'total_amount' => $this->order->total_amount + $this->order->shipping_cost,
            'url' => route('wholesale.customer.dashboard'),
        ];
    }
}
