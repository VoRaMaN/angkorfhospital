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
        Schema::table('rx_medicines', function (Blueprint $table) {
            if (!Schema::hasColumn('rx_medicines', 'category')) {
                $table->string('category')->nullable()->after('description');
            }
            if (!Schema::hasColumn('rx_medicines', 'unit')) {
                $table->string('unit')->nullable()->after('category');
            }
            if (!Schema::hasColumn('rx_medicines', 'dose_unit')) {
                $table->string('dose_unit')->nullable()->after('unit');
            }
            if (!Schema::hasColumn('rx_medicines', 'total_per_box')) {
                $table->integer('total_per_box')->nullable()->after('dose_unit');
            }
            if (!Schema::hasColumn('rx_medicines', 'reorder_quantity')) {
                $table->integer('reorder_quantity')->nullable()->after('total_per_box');
            }
            if (!Schema::hasColumn('rx_medicines', 'expiry_date')) {
                $table->date('expiry_date')->nullable()->after('reorder_quantity');
            }
            if (!Schema::hasColumn('rx_medicines', 'stock_quantity')) {
                $table->integer('stock_quantity')->default(0)->after('expiry_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rx_medicines', function (Blueprint $table) {
            $table->dropColumnIfExists(['category', 'unit', 'dose_unit', 'total_per_box', 'reorder_quantity', 'expiry_date', 'stock_quantity']);
        });
    }
};
