<?php

namespace App\Console\Commands;

use App\Enums\BillingStatusEnum;
use App\Models\Billing;
use Illuminate\Console\Command;

class MarkOverdueBillings extends Command
{
    protected $signature = 'billings:mark-overdue';

    protected $description = 'Mark unpaid bills from previous days as overdue';

    public function handle(): int
    {
        $count = Billing::whereIn('status', [BillingStatusEnum::PENDING, BillingStatusEnum::PARTIAL])
            ->whereDate('billing_date', '<', today())
            ->update(['status' => BillingStatusEnum::OVERDUE]);

        $this->info("Marked {$count} billing(s) as overdue.");

        return Command::SUCCESS;
    }
}
