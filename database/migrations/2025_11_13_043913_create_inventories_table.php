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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->string('item_name');
            $table->text('description')->nullable();
            $table->enum('type_of_supply', ['medication', 'lab_supply', 'medical_equipment', 'office_supply', 'cleaning_supply']);
            $table->integer('quantity')->default(0);
            $table->string('unit'); // 'tablets', 'boxes', 'liters', etc.
            $table->integer('minimum_stock')->default(0);
            $table->decimal('unit_price', 10, 2)->nullable();
            $table->string('supplier')->nullable();
            $table->string('location')->nullable();
            $table->date('expiry_date')->nullable();
            $table->text('notes')->nullable();
            $table->index('type_of_supply');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
