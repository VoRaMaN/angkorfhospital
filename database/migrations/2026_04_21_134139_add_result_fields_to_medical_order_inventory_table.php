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
        Schema::table('medical_order_inventory', function (Blueprint $table) {
            $table->string('result_value')->nullable()->after('notes');
            $table->string('result_unit')->nullable()->after('result_value');
            $table->text('result_notes')->nullable()->after('result_unit');
        });
    }

    public function down(): void
    {
        Schema::table('medical_order_inventory', function (Blueprint $table) {
            $table->dropColumn(['result_value', 'result_unit', 'result_notes']);
        });
    }
};
