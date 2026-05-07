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
        Schema::create('fet_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medical_order_id')->constrained()->cascadeOnDelete();

            // Patient info
            $table->string('female_patient_id')->nullable()->index();
            $table->string('female_patient_name')->nullable();
            $table->string('female_hn')->nullable();
            $table->string('female_dob')->nullable();
            $table->string('male_patient_id')->nullable()->index();
            $table->string('male_patient_name')->nullable();
            $table->string('male_hn')->nullable();
            $table->string('male_dob')->nullable();

            // Procedure
            $table->string('procedure')->default('FET');
            $table->string('fet_date')->nullable();
            $table->string('doctor')->nullable();

            // Embryo Thawing
            $table->string('freeze_datetime')->nullable();
            $table->string('thaw_datetime')->nullable();
            $table->string('thawing_media')->nullable();
            $table->integer('no_of_freeze')->nullable();
            $table->integer('no_of_thaw')->nullable();
            $table->string('lot_no')->nullable();
            $table->string('stage_of_freeze')->nullable();
            $table->integer('no_of_survival')->nullable();
            $table->string('exp_date')->nullable();
            $table->integer('no_of_remaining')->nullable();
            $table->string('thawing_by')->nullable();

            // Embryo Development Day 3
            $table->string('day3_datetime')->nullable();
            $table->string('day3_embryo_1')->nullable();
            $table->string('day3_embryo_2')->nullable();
            $table->string('day3_embryo_3')->nullable();
            $table->string('day3_embryo_4')->nullable();
            $table->string('day3_embryo_5')->nullable();

            // Embryo Development Day 5
            $table->string('day5_datetime')->nullable();
            $table->string('day5_embryo_1')->nullable();
            $table->string('day5_embryo_2')->nullable();
            $table->string('day5_embryo_3')->nullable();
            $table->string('day5_embryo_4')->nullable();
            $table->string('day5_embryo_5')->nullable();

            // Embryo for ET
            $table->integer('no_of_et')->nullable();
            $table->string('et_volume')->nullable();
            $table->integer('number_of_transfer')->nullable();
            $table->integer('et_day')->nullable();
            $table->string('et_catheter')->nullable();
            $table->integer('number_of_freeze_et')->nullable();
            $table->string('et_datetime')->nullable();
            $table->string('et_doctor')->nullable();
            $table->integer('number_of_discard')->nullable();
            $table->string('assisted_hatching')->nullable();
            $table->string('et_embryologist')->nullable();
            $table->string('embryologist_report')->nullable();
            $table->string('embryologist_approve')->nullable();
            $table->text('remark')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fet_reports');
    }
};
