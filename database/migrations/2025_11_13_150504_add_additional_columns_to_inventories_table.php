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
        Schema::table('inventories', function (Blueprint $table) {
            $table->string('category')->nullable()->after('description');
            $table->string('barcode')->nullable()->after('category');
            $table->string('dose_unit')->nullable()->after('unit');
            $table->integer('total_per_box')->nullable()->after('dose_unit');
            $table->decimal('selling_price', 10, 2)->nullable()->after('unit_price');
            $table->integer('alert_days')->default(90)->after('expiry_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            $table->dropColumn([
                'category',
                'barcode',
                'dose_unit',
                'total_per_box',
                'selling_price',
                'alert_days',
            ]);
        });
    }
};
