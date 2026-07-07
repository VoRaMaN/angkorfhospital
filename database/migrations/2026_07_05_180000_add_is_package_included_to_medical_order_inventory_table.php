<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('medical_order_inventory', function (Blueprint $table) {
            $table->boolean('is_package_included')->default(false)->after('selling_price');
        });

        // Backfill: before this column existed, package-included items were only
        // marked by the notes text written by the Process screen.
        DB::table('medical_order_inventory')
            ->where('notes', 'like', '%Include Package - Not counted in billing%')
            ->update(['is_package_included' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medical_order_inventory', function (Blueprint $table) {
            $table->dropColumn('is_package_included');
        });
    }
};
