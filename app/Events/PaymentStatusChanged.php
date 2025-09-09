<?php

namespace App\Events;

use App\Models\Order;
use App\Enums\PaymentStatus;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentStatusChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;
    public $oldStatus;
    public $newStatus;

    public function __construct(Order $order, $oldStatus, $newStatus)
    {
        $this->order = $order;
        $this->oldStatus = $this->convertToString($oldStatus);
        $this->newStatus = $this->convertToString($newStatus);
    }

    /**
     * Convert enum or string to string
     */
    private function convertToString($status)
    {
        if ($status instanceof PaymentStatus) {
            return $status->value;
        }

        if (is_string($status)) {
            return $status;
        }

        // Fallback for other types
        return (string) $status;
    }
}
