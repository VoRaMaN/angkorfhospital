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
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->index('user_id');
            $table->string('first_name');
            $table->string('last_name');
            $table->unsignedBigInteger('role_id');
            $table->index('role_id');
            $table->unsignedBigInteger('department_id')->nullable();
            $table->index('department_id');
            $table->string('contact_number');
            $table->date('hire_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
