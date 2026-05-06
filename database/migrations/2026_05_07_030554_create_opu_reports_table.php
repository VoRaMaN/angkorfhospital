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
        Schema::create('opu_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medical_order_id')->constrained()->cascadeOnDelete();
            $table->string('female_patient_id')->nullable();
            $table->string('male_patient_id')->nullable();
            $table->string('procedure')->nullable()->default('OPU-ICSI');
            $table->foreignId('doctor_id')->nullable()->constrained('staff')->nullOnDelete();

            // OPU datetime
            $table->timestamp('opu_datetime')->nullable();

            // Maturation
            $table->integer('no_of_opu_right')->nullable();
            $table->integer('no_of_opu_left')->nullable();
            $table->timestamp('maturation_datetime')->nullable();
            $table->integer('m_ii')->nullable();
            $table->integer('m_i')->nullable();
            $table->integer('gv')->nullable();
            $table->integer('post_mature')->nullable();
            $table->integer('maturation_abnormal')->nullable();
            $table->integer('maturation_dead')->nullable();

            // Fertilization
            $table->timestamp('fertilization_datetime')->nullable();
            $table->integer('pn2')->nullable();
            $table->integer('pn1')->nullable();
            $table->integer('pn3')->nullable();
            $table->integer('pn4')->nullable();
            $table->integer('no_pn')->nullable();
            $table->integer('fert_dead')->nullable();

            // Sperm Preparation
            $table->timestamp('sperm_prep_datetime')->nullable();
            $table->decimal('sperm_volume_ml', 6, 2)->nullable();
            $table->decimal('sperm_count_per_ml', 10, 2)->nullable();
            $table->decimal('sperm_total_count', 10, 2)->nullable();
            $table->decimal('sperm_motile_per_ml', 10, 2)->nullable();
            $table->decimal('sperm_total_motile', 10, 2)->nullable();
            $table->decimal('sperm_motility_pct', 5, 2)->nullable();
            $table->enum('sperm_type', ['fresh', 'frozen'])->nullable();

            // Embryo Freezing
            $table->timestamp('embryo_freeze_datetime')->nullable();
            $table->string('freeze_day')->nullable();
            $table->string('freeze_stage')->nullable();
            $table->integer('no_of_embryo')->nullable();
            $table->integer('no_of_straw')->nullable();
            $table->string('freeze_position')->nullable();
            $table->string('freeze_method')->nullable();
            $table->string('freeze_media')->nullable();

            // Embryo Development Day 3 (JSON: array of {slot, grade})
            $table->timestamp('day3_datetime')->nullable();
            $table->string('day3_checked_by')->nullable();
            $table->json('day3_embryos')->nullable();

            // Embryo Development Day 5 (JSON: array of {slot, grade})
            $table->timestamp('day5_datetime')->nullable();
            $table->string('day5_checked_by')->nullable();
            $table->json('day5_embryos')->nullable();

            // Embryo for ET
            $table->integer('et_no')->nullable();
            $table->string('et_day')->nullable();
            $table->timestamp('et_datetime')->nullable();
            $table->boolean('assisted_hatching')->default(false);
            $table->string('et_volume')->nullable()->default('15µl');
            $table->string('et_catheter')->nullable();
            $table->string('et_doctor')->nullable();
            $table->string('et_embryologist')->nullable();
            $table->integer('number_of_transfer')->nullable();
            $table->integer('number_of_freeze')->nullable();
            $table->integer('number_of_discard')->nullable();

            // Notes
            $table->text('remark')->nullable();
            $table->text('embryologist_report')->nullable();
            $table->text('embryologist_approve')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opu_reports');
    }
};
