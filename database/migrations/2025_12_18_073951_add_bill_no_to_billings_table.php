<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('billings', function (Blueprint $table) {
            $table->string('bill_no', 20)->nullable()->after('id');
        });

        // Generate bill numbers for existing records
        $billings = DB::table('billings')->orderBy('id')->get();
        foreach ($billings as $billing) {
            $date = \Carbon\Carbon::parse($billing->billing_date);
            $yearMonth = $date->format('ym'); // e.g., 2512 for Dec 2025

            // Get the count of bills in this month to generate sequential number
            $count = DB::table('billings')
                ->where('bill_no', 'like', $yearMonth.'-%')
                ->count();

            $billNo = $yearMonth.'-'.str_pad($count + 1, 4, '0', STR_PAD_LEFT);

            DB::table('billings')
                ->where('id', $billing->id)
                ->update(['bill_no' => $billNo]);
        }

        // Now make it unique and not nullable
        Schema::table('billings', function (Blueprint $table) {
            $table->string('bill_no', 20)->unique()->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('billings', function (Blueprint $table) {
            $table->dropColumn('bill_no');
        });
    }
};
