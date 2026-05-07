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
        Schema::create('hormone_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medical_order_id')->constrained('medical_orders')->cascadeOnDelete();
            $table->string('patient_id')->nullable();
            $table->string('specimen')->default('Serum');
            $table->string('collection_date')->nullable();
            $table->string('collection_time')->nullable();
            $table->string('received_date')->nullable();
            $table->string('received_time')->nullable();
            // Hormone values (nullable — not all tests may be ordered)
            $table->decimal('lh', 10, 2)->nullable();
            $table->decimal('fsh', 10, 2)->nullable();
            $table->decimal('prolactin', 10, 2)->nullable();
            $table->decimal('estradiol', 10, 2)->nullable();
            $table->decimal('progesterone', 10, 2)->nullable();
            $table->decimal('testosterone', 10, 2)->nullable();
            $table->decimal('tsh', 10, 3)->nullable();
            $table->decimal('amh', 10, 2)->nullable();
            $table->decimal('beta_hcg', 12, 2)->nullable();
            // Flags for "not tested" vs "negative/dash" vs a value
            // We use null = not entered, empty string / "-" handled in display
            $table->string('remark')->nullable();
            $table->string('reported_by')->nullable();
            $table->string('reported_date')->nullable();
            $table->string('reported_time')->nullable();
            $table->string('approved_by')->nullable();
            $table->string('approved_date')->nullable();
            $table->string('approved_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hormone_reports');
    }
};
