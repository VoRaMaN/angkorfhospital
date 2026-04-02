<?php

namespace App\Observers;

use App\Models\Billing;

class BillingObserver
{
    /**
     * Handle the Billing "creating" event.
     */
    public function creating(Billing $billing): void
    {
        // Generate bill number if not already set
        if (empty($billing->bill_no)) {
            $billing->bill_no = Billing::generateBillNo($billing->billing_date);
        }
    }
}
