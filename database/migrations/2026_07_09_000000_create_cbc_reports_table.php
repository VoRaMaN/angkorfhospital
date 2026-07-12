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
        Schema::create('cbc_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medical_order_id')->constrained('medical_orders')->cascadeOnDelete();
            $table->string('patient_id')->nullable();
            $table->string('lab_id')->nullable();
            $table->string('requested_by')->nullable();
            $table->string('requested_date')->nullable();
            $table->string('analysis_date')->nullable();
            // Complete Blood Count
            $table->decimal('wbc', 10, 2)->nullable();
            $table->decimal('rbc', 10, 2)->nullable();
            $table->decimal('hemoglobin', 10, 2)->nullable();
            $table->decimal('hematocrit', 10, 2)->nullable();
            $table->decimal('mcv', 10, 2)->nullable();
            $table->decimal('mch', 10, 2)->nullable();
            $table->decimal('mchc', 10, 2)->nullable();
            $table->decimal('platelets', 10, 2)->nullable();
            $table->decimal('rdw', 10, 2)->nullable();
            // Differential White Cell Count
            $table->decimal('neutrophils', 10, 2)->nullable();
            $table->decimal('lymphocytes', 10, 2)->nullable();
            $table->decimal('monocytes', 10, 2)->nullable();
            $table->decimal('eosinophils', 10, 2)->nullable();
            $table->decimal('basophils', 10, 2)->nullable();
            $table->text('remark')->nullable();
            $table->string('reported_by')->nullable();
            $table->string('reported_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cbc_reports');
    }
};
