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
        Schema::table('appointments', function (Blueprint $table) {
            $table->integer('duration_minutes')->default(30)->after('appointment_date_time');
            $table->enum('appointment_type', ['consultation', 'emergency', 'follow_up', 'procedure', 'checkup', 'telemedicine', 'screening', 'therapy'])->default('consultation')->after('duration_minutes');
            $table->text('notes')->nullable()->after('reason_for_visit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn(['duration_minutes', 'appointment_type', 'notes']);
        });
    }
};
