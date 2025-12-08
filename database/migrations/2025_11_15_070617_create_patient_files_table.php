<?php

use App\Enums\PatientFileTypeEnum;
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
        Schema::create('patient_files', function (Blueprint $table) {
            $table->id();
            $table->string('patient_id', 9);
            $table->unsignedBigInteger('file_id');
            // File type enum: PatientFileTypeEnum
            $table->enum('type', array_map(fn ($case) => $case->value, PatientFileTypeEnum::cases()));
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_files');
    }
};
