<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DebtDueReminder implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $transactionId;
    public $invoiceNumber;
    public $customerName;
    public $debtAmount;
    public $daysOverdue;

    public function __construct($transactionId, $invoiceNumber, $customerName, $debtAmount, $daysOverdue)
    {
        $this->transactionId = $transactionId;
        $this->invoiceNumber = $invoiceNumber;
        $this->customerName = $customerName;
        $this->debtAmount = $debtAmount;
        $this->daysOverdue = $daysOverdue;
    }

    public function broadcastOn(): array
    {
        return [new Channel('notifications')];
    }

    public function broadcastWith(): array
    {
        return [
            'type'    => 'debt_due',
            'title'   => 'Hutang Jatuh Tempo',
            'message' => "{$this->customerName} — {$this->invoiceNumber} ({$this->daysOverdue} hari) — Rp " . number_format($this->debtAmount, 0, ',', '.'),
        ];
    }
}
