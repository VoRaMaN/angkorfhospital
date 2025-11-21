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
        Schema::create('medical_orders', function (Blueprint $table) {
            $table->id();
            $table->string('patient_id', 13)->nullable();
            $table->unsignedBigInteger('staff_id')->nullable();
            $table->index('staff_id');
            $table->string('order_details');
            $table->string('status')->default('pending'); // pending, processing, processed, complete, cancel, rejected
            $table->string('priority')->default('routine'); // routine, urgent, stat
            $table->text('notes')->nullable();
            $table->timestamp('ordered_at');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_orders');
    }
};
