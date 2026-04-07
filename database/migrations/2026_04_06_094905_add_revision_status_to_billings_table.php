<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE billings MODIFY COLUMN status ENUM('pending', 'paid', 'overdue', 'partial', 'written_off', 'cancelled', 'revision') NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE billings MODIFY COLUMN status ENUM('pending', 'paid', 'overdue', 'partial', 'written_off', 'cancelled') NOT NULL DEFAULT 'pending'");
    }
};
