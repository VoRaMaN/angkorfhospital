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
        Schema::table('fet_reports', function (Blueprint $table) {
            $table->integer('picture_day')->nullable()->after('day5_embryo_5');
            $table->string('picture_datetime', 50)->nullable()->after('picture_day');
            $table->json('embryo_pictures')->nullable()->after('picture_datetime');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fet_reports', function (Blueprint $table) {
            $table->dropColumn(['picture_day', 'picture_datetime', 'embryo_pictures']);
        });
    }
};
