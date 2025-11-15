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
        Schema::create('medical_order_inventory', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('medical_order_id');
            $table->unsignedBigInteger('inventory_id')->nullable(); // Nullable for non-inventory items
            $table->string('item_type'); // lab, medication, procedure, imaging, consultation, therapy, supply
            $table->string('item_name'); // Name/description of the item
            $table->text('details')->nullable(); // Additional details (instructions, test parameters, etc.)
            $table->string('dosage')->nullable(); // For medications
            $table->string('frequency')->nullable(); // For medications (e.g., "twice daily", "every 4 hours")
            $table->string('route')->nullable(); // For medications (oral, IV, IM, etc.)
            $table->integer('quantity_required')->default(1);
            $table->string('status')->default('pending'); // pending, in_progress, completed, cancelled
            $table->text('notes')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['medical_order_id', 'item_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_order_inventory');
    }
};
