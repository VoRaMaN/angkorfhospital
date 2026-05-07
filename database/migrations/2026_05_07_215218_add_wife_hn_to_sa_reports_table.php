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
        Schema::table('sa_reports', function (Blueprint $table) {
            $table->string('wife_hn')->nullable()->after('wife_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sa_reports', function (Blueprint $table) {
            $table->dropColumn('wife_hn');
        });
    }
};
