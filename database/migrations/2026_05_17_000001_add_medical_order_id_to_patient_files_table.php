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
        Schema::table('patient_files', function (Blueprint $table) {
            $table->foreignId('medical_order_id')
                ->nullable()
                ->constrained('medical_orders')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patient_files', function (Blueprint $table) {
            $table->dropForeign(['medical_order_id']);
            $table->dropColumn('medical_order_id');
        });
    }
};
