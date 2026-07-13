<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Re-run the notes-sentinel backfill: rows created between the original
     * backfill and clients picking up the flag-sending frontend were saved
     * with the sentinel note but is_package_included = 0. Idempotent.
     */
    public function up(): void
    {
        DB::table('medical_order_inventory')
            ->where('notes', 'like', '%Include Package - Not counted in billing%')
            ->update(['is_package_included' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Data backfill; nothing to reverse.
    }
};
