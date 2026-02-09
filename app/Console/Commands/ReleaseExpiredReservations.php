<?php

namespace App\Console\Commands;

use App\Services\OrderService;
use Illuminate\Console\Command;

class ReleaseExpiredReservations extends Command
{
    protected $signature = 'reservations:release-expired';

    protected $description = 'Release expired stock reservations and cancel associated orders';

    public function handle(OrderService $orderService): int
    {
        $count = $orderService->releaseAllExpired();

        if ($count === 0) {
            $this->info('No expired reservations found.');
        } else {
            $this->info("Released {$count} expired reservation(s).");
        }

        return self::SUCCESS;
    }
}
