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
        Schema::create('semen_analysis_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medical_order_id')->constrained()->cascadeOnDelete();
            $table->string('patient_id')->nullable()->index();

            // Basic info
            $table->string('wife_name')->nullable();
            $table->integer('abstinence_days')->nullable();
            $table->string('appearance')->nullable();
            $table->string('liquefaction')->nullable();
            $table->string('viscosity')->nullable();
            $table->decimal('ph', 4, 2)->nullable();

            // Main parameters
            $table->decimal('viability', 6, 2)->nullable();
            $table->decimal('volume', 6, 2)->nullable();
            $table->decimal('count_per_ml', 10, 2)->nullable();
            $table->decimal('total_count', 10, 2)->nullable();
            $table->decimal('motile', 10, 2)->nullable();
            $table->decimal('total_motile', 10, 2)->nullable();
            $table->decimal('motility', 6, 2)->nullable();

            // Motility rates
            $table->decimal('motility_4_rapid', 6, 2)->nullable();
            $table->decimal('motility_3_medium', 6, 2)->nullable();
            $table->decimal('motility_2_slow', 6, 2)->nullable();
            $table->decimal('motility_1_static', 6, 2)->nullable();

            // WBC & Morphology
            $table->string('wbc')->nullable();
            $table->decimal('morphology_normal', 6, 2)->nullable();
            $table->decimal('morphology_abnormal', 6, 2)->nullable();
            $table->decimal('head_defect', 6, 2)->nullable();
            $table->decimal('neck_defect', 6, 2)->nullable();
            $table->decimal('tail_defect', 6, 2)->nullable();

            // Vial & Times
            $table->integer('no_of_vial')->nullable();
            $table->string('ejaculation_time')->nullable();
            $table->string('examination_time')->nullable();
            $table->string('receive_time')->nullable();
            $table->string('finish_time')->nullable();

            // Sign-off
            $table->text('remark')->nullable();
            $table->string('reported_by')->nullable();
            $table->date('reported_date')->nullable();
            $table->string('reported_time')->nullable();
            $table->string('approved_by')->nullable();
            $table->date('approved_date')->nullable();
            $table->string('approved_time')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('semen_analysis_reports');
    }
};
