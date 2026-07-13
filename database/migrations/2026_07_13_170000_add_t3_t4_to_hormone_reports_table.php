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
        Schema::table('hormone_reports', function (Blueprint $table) {
            $table->decimal('t3', 10, 2)->nullable()->after('tsh');
            $table->decimal('t4', 10, 2)->nullable()->after('t3');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hormone_reports', function (Blueprint $table) {
            $table->dropColumn(['t3', 't4']);
        });
    }
};
