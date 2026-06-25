<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Broadcasts live dashboard counter updates.
 *
 * BEFORE: Used public Channel('dashboard') — any unauthenticated listener could subscribe.
 * AFTER: Uses PrivateChannel('dashboard') — requires authentication via broadcasting auth.
 *
 * Authorization is handled in routes/channels.php.
 */
class DashboardUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $totalTransactionsToday;
    public float $totalRevenueToday;
    public int $lowStockCount;
    public int $pendingDebtsCount;

    public function __construct(
        int   $totalTransactionsToday,
        float $totalRevenueToday,
        int   $lowStockCount,
        int   $pendingDebtsCount
    ) {
        $this->totalTransactionsToday = $totalTransactionsToday;
        $this->totalRevenueToday      = $totalRevenueToday;
        $this->lowStockCount          = $lowStockCount;
        $this->pendingDebtsCount      = $pendingDebtsCount;
    }

    /**
     * Private channel — only authenticated users can subscribe.
     * Authorization defined in routes/channels.php.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('dashboard'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'dashboard.updated';
    }
}
