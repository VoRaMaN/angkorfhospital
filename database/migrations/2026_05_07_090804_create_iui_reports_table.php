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
        Schema::create('iui_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medical_order_id')->constrained()->cascadeOnDelete();
            $table->string('patient_id')->nullable()->index();

            // Wife info
            $table->string('wife_name')->nullable();
            $table->string('wife_hn')->nullable();

            // Sperm type
            $table->boolean('owner_sperm')->default(false);
            $table->boolean('donor_sperm')->default(false);
            $table->boolean('fresh_sperm')->default(false);
            $table->boolean('frozen_sperm')->default(false);
            $table->integer('frozen_vial')->nullable();

            // Basic params
            $table->integer('abstinence_days')->nullable();
            $table->string('appearance')->nullable();
            $table->string('liquefaction')->nullable();
            $table->string('viscosity')->nullable();

            // Pre-preparation
            $table->decimal('pre_volume', 6, 2)->nullable();
            $table->decimal('pre_count', 10, 2)->nullable();
            $table->decimal('pre_total_count', 10, 2)->nullable();
            $table->decimal('pre_motile', 10, 2)->nullable();
            $table->decimal('pre_total_motile', 10, 2)->nullable();
            $table->decimal('pre_motility', 6, 2)->nullable();
            $table->decimal('pre_motility_4_rapid', 6, 2)->nullable();
            $table->decimal('pre_motility_3_medium', 6, 2)->nullable();
            $table->decimal('pre_motility_2_slow', 6, 2)->nullable();
            $table->decimal('pre_motility_1_static', 6, 2)->nullable();

            // Post-preparation
            $table->decimal('post_volume', 6, 2)->nullable();
            $table->decimal('post_count', 10, 2)->nullable();
            $table->decimal('post_total_count', 10, 2)->nullable();
            $table->decimal('post_motile', 10, 2)->nullable();
            $table->decimal('post_total_motile', 10, 2)->nullable();
            $table->decimal('post_motility', 6, 2)->nullable();
            $table->decimal('post_motility_4_rapid', 6, 2)->nullable();
            $table->decimal('post_motility_3_medium', 6, 2)->nullable();
            $table->decimal('post_motility_2_slow', 6, 2)->nullable();
            $table->decimal('post_motility_1_static', 6, 2)->nullable();

            // Times
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
        Schema::dropIfExists('iui_reports');
    }
};
