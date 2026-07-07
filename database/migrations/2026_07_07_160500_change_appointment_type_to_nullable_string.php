<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * The UI treats appointment_type as optional free text (IVF flag
     * appointments often have no type), but the column was still the
     * original NOT NULL enum — strict-mode MySQL rejects both null and
     * non-enum values. Raw MODIFY because change() can't alter enums.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE appointments MODIFY appointment_type VARCHAR(100) NULL DEFAULT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("UPDATE appointments SET appointment_type = 'consultation' WHERE appointment_type IS NULL");
        DB::statement("ALTER TABLE appointments MODIFY appointment_type ENUM('consultation','emergency','follow_up','procedure','checkup','telemedicine','screening','therapy') NOT NULL DEFAULT 'consultation'");
    }
};
