<?php

namespace App\Events;

use App\Models\Transaction;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DebtSubmitted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int    $transactionId;
    public string $invoiceNumber;
    public float  $debtAmount;
    public string $customerName;
    public string $cashierName;

    /**
     * Create a new event instance.
     */
    public function __construct(Transaction $transaction)
    {
        $this->transactionId = $transaction->id;
        $this->invoiceNumber = $transaction->invoice_number;
        $this->debtAmount    = (float) $transaction->debt_amount;
        $this->customerName  = optional($transaction->customer)->name ?? 'Walk-in Customer';
        $this->cashierName   = optional($transaction->user)->name ?? 'Unknown';
    }

    /**
     * Broadcast on a private channel so only owner/admin can listen.
     * We use a single shared private channel for all debt notifications.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('debt-approvals'),
        ];
    }

    /**
     * Custom event name consumed by Echo on the frontend.
     */
    public function broadcastAs(): string
    {
        return 'debt.submitted';
    }
}
