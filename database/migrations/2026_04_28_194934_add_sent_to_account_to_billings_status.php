<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE billings MODIFY COLUMN status ENUM('pending','paid','overdue','partial','written_off','cancelled','revision','revised','sent_to_account') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE billings MODIFY COLUMN status ENUM('pending','paid','overdue','partial','written_off','cancelled','revision','revised') NOT NULL DEFAULT 'pending'");
    }
};
