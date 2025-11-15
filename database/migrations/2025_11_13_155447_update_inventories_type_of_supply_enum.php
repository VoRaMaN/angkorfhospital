<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE inventories MODIFY COLUMN type_of_supply ENUM('medication', 'rx_medicine', 'lab_supply', 'medical_equipment', 'office_supply', 'cleaning_supply')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE inventories MODIFY COLUMN type_of_supply ENUM('medication', 'lab_supply', 'medical_equipment', 'office_supply', 'cleaning_supply')");
    }
};
