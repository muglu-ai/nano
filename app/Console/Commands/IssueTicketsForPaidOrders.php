<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Ticket\TicketOrder;
use App\Services\TicketIssuanceService;
use Illuminate\Support\Facades\Log;

class IssueTicketsForPaidOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tickets:issue-for-paid-orders 
                            {--order-id= : Specific order ID to process}
                            {--dry-run : Show what would be done without actually doing it}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Issue tickets for paid orders that don\'t have tickets yet';

    protected $ticketIssuanceService;

    public function __construct(TicketIssuanceService $ticketIssuanceService)
    {
        parent::__construct();
        $this->ticketIssuanceService = $ticketIssuanceService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $orderId = $this->option('order-id');

        if ($dryRun) {
            $this->info('🔍 DRY RUN MODE - No changes will be made');
        }

        // Get paid orders
        $query = TicketOrder::where('status', 'paid')
            ->with(['registration.delegates', 'items.ticketType']);

        if ($orderId) {
            $query->where('id', $orderId);
        }

        $orders = $query->get();

        if ($orders->isEmpty()) {
            $this->info('No paid orders found.');
            return 0;
        }

        $this->info("Found {$orders->count()} paid order(s)");

        $successCount = 0;
        $skipCount = 0;
        $errorCount = 0;

        foreach ($orders as $order) {
            $this->line("Processing Order #{$order->id} ({$order->order_no})...");

            // Check if tickets already exist
            $delegates = $order->registration->delegates ?? collect();
            $existingTicketsCount = 0;
            
            foreach ($delegates as $delegate) {
                if ($delegate->ticket) {
                    $existingTicketsCount++;
                }
            }

            if ($existingTicketsCount === $delegates->count() && $delegates->count() > 0) {
                $this->warn("  ⏭️  Skipping - All delegates already have tickets");
                $skipCount++;
                continue;
            }

            if ($dryRun) {
                $this->info("  ✅ Would issue tickets for {$delegates->count()} delegate(s)");
                $successCount++;
                continue;
            }

            // Issue tickets
            try {
                $result = $this->ticketIssuanceService->issueTicketsForOrder($order);
                if ($result) {
                    $this->info("  ✅ Tickets issued successfully");
                    $successCount++;
                } else {
                    $this->error("  ❌ Failed to issue tickets");
                    $errorCount++;
                }
            } catch (\Exception $e) {
                $this->error("  ❌ Error: " . $e->getMessage());
                Log::error('IssueTicketsForPaidOrders: Error', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                ]);
                $errorCount++;
            }
        }

        $this->newLine();
        $this->info("Summary:");
        $this->info("  ✅ Success: {$successCount}");
        $this->info("  ⏭️  Skipped: {$skipCount}");
        $this->info("  ❌ Errors: {$errorCount}");

        return 0;
    }
}
