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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->index('user_id');
            
            // Personal Info
            $table->string('title')->nullable(); // Mrs., Mr.
            $table->string('first_name');
            $table->string('last_name');
            $table->string('native_name')->nullable();
            $table->string('native_surname')->nullable();
            $table->date('date_of_birth');
            $table->string('identification_number')->nullable(); // ID Card / Passport
            $table->string('marital_status')->nullable(); // Single, Married, etc.
            $table->string('nationality')->nullable();
            $table->string('religion')->nullable();
            $table->string('race')->nullable();
            $table->enum('gender', ['male', 'female', 'other']);
            
            // Address
            $table->text('address')->nullable(); // Keep for legacy or full string
            $table->string('address_building_village')->nullable();
            $table->string('address_moo')->nullable();
            $table->string('address_soi')->nullable();
            $table->string('address_road')->nullable();
            $table->string('address_sub_district')->nullable();
            $table->string('address_district')->nullable();
            $table->string('address_province')->nullable();
            $table->string('address_zip_code')->nullable();
            
            // Contact
            $table->string('phone_number'); // Mobile
            $table->string('home_phone_number')->nullable();
            $table->string('email')->nullable();
            
            // Employment
            $table->string('occupation')->nullable();
            $table->string('company_name')->nullable();
            $table->string('company_phone_number')->nullable();
            
            // Emergency Contact
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_relationship')->nullable();
            $table->string('emergency_contact_description')->nullable(); // Description Other
            $table->boolean('emergency_contact_same_address')->default(false);
            $table->string('emergency_contact_address')->nullable();
            $table->string('emergency_contact_road')->nullable();
            $table->string('emergency_contact_sub_district')->nullable();
            $table->string('emergency_contact_district')->nullable();
            $table->string('emergency_contact_province')->nullable();
            $table->string('emergency_contact_zip_code')->nullable();
            $table->string('emergency_contact_home_phone')->nullable();
            $table->string('emergency_contact_mobile_phone')->nullable();
            $table->string('emergency_contact_email')->nullable();
            
            // Payment & Insurance
            $table->string('payment_method')->nullable();
            $table->string('contract_name')->nullable();
            $table->string('insurance_name')->nullable();
            $table->text('insurance_info')->nullable();
            $table->string('agent_name')->nullable();
            $table->string('patient_type')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
