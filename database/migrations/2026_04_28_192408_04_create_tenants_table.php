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
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();

            // 1. Primary Personal Information
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->enum('gender', ['male', 'female', 'other']);
            $table->date('date_of_birth')->nullable();
            $table->string('blood_group')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('profile_photo')->nullable();

            // 2. Contact & Communication
            $table->string('phone');
            $table->string('secondary_phone')->nullable();
            $table->string('email')->unique()->nullable();
            $table->text('permanent_address')->nullable();
            $table->text('current_address')->nullable();

            // 3. Family & Guardian Details
            $table->string('father_name')->nullable();
            $table->string('father_phone')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('mother_phone')->nullable();
            $table->string('local_guardian_name')->nullable();
            $table->string('local_guardian_phone')->nullable();
            $table->string('local_guardian_relationship')->nullable();

            // 4. Official Identification
            $table->string('citizenship_number')->nullable();
            $table->string('passport_number')->nullable();
            $table->string('citizenship_issued_district')->nullable();
            $table->string('citizenship_issued_date')->nullable();
            $table->string('id_document_front')->nullable();
            $table->string('id_document_back')->nullable();

            // 5. Educational / Professional Details
            $table->enum('occupation_status', ['student', 'job_holder'])->nullable();
            $table->string('organization_name')->nullable();
            $table->string('level_designation')->nullable();
            $table->string('roll_number_employee_id')->nullable();
            $table->string('organization_contact')->nullable();

            // 6. Health & Emergency
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->text('known_allergies')->nullable();
            $table->text('chronic_conditions')->nullable();
            $table->text('regular_medications')->nullable();

            // 7. Financial & Booking Preferences
            $table->date('joined_date')->nullable();
            $table->decimal('security_deposit', 10, 2)->nullable();
            $table->decimal('monthly_rent_agreed', 10, 2)->nullable();
            $table->enum('meal_preference', ['veg', 'non_veg', 'special_diet'])->nullable();
            $table->text('addon_services')->nullable();
            $table->string('referral_source')->nullable();

            // System
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
