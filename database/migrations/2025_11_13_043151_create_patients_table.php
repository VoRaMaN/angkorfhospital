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
            $table->string('id', 13)->primary(); // Custom format: YY/XXXXXXXXXX
            $table->unsignedBigInteger('user_id')->nullable();
            $table->index('user_id');
            $table->enum('title', ['Mr.', 'Mrs.', 'Ms.'])->nullable();
            $table->string('name');
            $table->string('surname');
            $table->string('khmer_china_name')->nullable();
            $table->string('khmer_china_surname')->nullable();
            $table->integer('date_of_birth_day');
            $table->integer('date_of_birth_month');
            $table->integer('date_of_birth_year');
            $table->enum('gender', ['Male', 'Female', 'Other'])->nullable();
            $table->string('id_card_or_passport')->nullable();
            $table->enum('marital_status', ['Single', 'Married', 'Divorced', 'Widowed'])->nullable();
            $table->string('nationality');
            $table->string('religion')->nullable();
            $table->string('race')->nullable();

            // Address
            $table->text('address')->nullable();
            $table->string('building_village')->nullable();
            $table->string('moo')->nullable();
            $table->string('soi')->nullable();
            $table->string('road')->nullable();
            $table->string('sub_district')->nullable();
            $table->string('district')->nullable();
            $table->string('province')->nullable();
            $table->string('zip_code')->nullable();

            // Contact
            $table->string('home_phone')->nullable();
            $table->string('mobile_phone')->nullable();
            $table->string('email')->nullable();
            $table->string('occupation')->nullable();
            $table->string('company_name')->nullable();
            $table->string('company_phone')->nullable();

            // Emergency Contact
            $table->string('emergency_contact_name')->nullable();
            $table->enum('emergency_contact_relationship', ['Spouse', 'Parent', 'Sibling', 'Friend', 'Other'])->nullable();
            $table->string('emergency_contact_description_other')->nullable();
            $table->boolean('emergency_contact_address_same_as_patient')->nullable();
            $table->text('emergency_contact_address')->nullable();
            $table->string('emergency_contact_road')->nullable();
            $table->string('emergency_contact_sub_district')->nullable();
            $table->string('emergency_contact_district')->nullable();
            $table->string('emergency_contact_province')->nullable();
            $table->string('emergency_contact_zip_code')->nullable();
            $table->string('emergency_contact_home_phone')->nullable();
            $table->string('emergency_contact_mobile_phone')->nullable();
            $table->string('emergency_contact_email')->nullable();

            // Payment
            $table->enum('payment_method', ['Corporate Contract', 'Self-Pay', 'Insurance'])->nullable();
            $table->string('contract_name')->nullable();
            $table->string('insurance_name')->nullable();
            $table->unsignedBigInteger('staff_id')->nullable();
            $table->index('staff_id'); // Staff with role Doctor
            $table->enum('patient_type', ['Patient', 'Customer', 'Dependent'])->nullable();

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
