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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id');
            $table->index('patient_id');
            $table->unsignedBigInteger('staff_id');
            $table->index('staff_id');
            $table->dateTime('appointment_date_time');
            $table->enum('status', ['scheduled', 'confirmed', 'completed', 'cancelled']);
            $table->text('reason_for_visit')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
