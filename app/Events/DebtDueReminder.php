<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
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
    public $branchId;

    public function __construct($transactionId, $invoiceNumber, $customerName, $debtAmount, $daysOverdue, $branchId = null)
    {
        $this->transactionId = $transactionId;
        $this->invoiceNumber = $invoiceNumber;
        $this->customerName  = $customerName;
        $this->debtAmount    = $debtAmount;
        $this->daysOverdue   = $daysOverdue;
        $this->branchId      = $branchId;
    }

    public function broadcastOn(): array
    {
        // Broadcast on the branch-scoped channel — managers only see debt
        // reminders for their own branch. Owner/admin subscribe to all branches.
        $branch = $this->branchId ?? 0;
        return [new PrivateChannel("notifications.{$branch}")];
    }

    public function broadcastAs(): string
    {
        return 'DebtDueReminder';
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
