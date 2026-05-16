<?php

namespace App\Observers;

use App\Models\LaundryOrder;
use App\Services\Finance\AutoJournalService;

class LaundryOrderObserver
{
    public function __construct(protected AutoJournalService $service) {}

    public function updated(LaundryOrder $order): void
    {
        try {
            $this->service->transaksiLaundry($order);
        } catch (\Throwable $e) {
            \Log::error("AutoJournal laundry failed for order #{$order->id}: {$e->getMessage()}");
        }
    }

    public function created(LaundryOrder $order): void
    {
        try {
            $this->service->transaksiLaundry($order);
        } catch (\Throwable $e) {
            \Log::error("AutoJournal laundry created failed for order #{$order->id}: {$e->getMessage()}");
        }
    }
}
