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
            $table->string('patient_id', 9);
            $table->unsignedBigInteger('staff_id');
            $table->index('staff_id');
            $table->dateTime('appointment_date_time');
            $table->enum('status', ['scheduled', 'confirmed', 'arrived', 'in_progress', 'completed', 'cancelled', 'no_show', 'rescheduled']);
            $table->text('reason_for_visit')->nullable();
            $table->boolean('is_hormone_test')->default(false);
            $table->boolean('is_tvs')->default(false);
            $table->time('opu_time')->nullable();
            $table->time('et_fet_time')->nullable();
            $table->boolean('is_beta_hcg')->default(false);
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
