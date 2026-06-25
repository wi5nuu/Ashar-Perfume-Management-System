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
        Schema::table('users', function (Blueprint $table) {
            // Personal Information
            $table->string('nik', 16)->nullable()->unique()->after('full_name');
            $table->enum('gender', ['male', 'female'])->nullable()->after('nik');
            $table->string('place_of_birth')->nullable()->after('gender');
            $table->date('date_of_birth')->nullable()->after('place_of_birth');
            $table->enum('religion', ['islam', 'protestan', 'katolik', 'hindu', 'buddha', 'khonghucu', 'others'])->nullable()->after('date_of_birth');
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed'])->nullable()->after('religion');
            
            // Educational & Job Info
            $table->string('last_education')->nullable()->after('marital_status');
            $table->date('join_date')->nullable()->after('last_education');
            $table->string('employee_id')->nullable()->unique()->after('join_date'); // Perusahaan Global biasanya punya ID Karyawan
            $table->enum('employment_status', ['permanent', 'contract', 'probation', 'internship'])->default('contract')->after('employee_id');
            
            // Financial Info
            $table->string('bank_name')->nullable()->after('employment_status');
            $table->string('bank_account_number')->nullable()->after('bank_name');
            $table->string('bank_account_holder')->nullable()->after('bank_account_number');
            $table->string('npwp')->nullable()->after('bank_account_holder');
            $table->decimal('basic_salary', 15, 2)->default(0)->after('npwp');
            
            // Emergency Contact
            $table->string('emergency_contact_name')->nullable()->after('living_address');
            $table->string('emergency_contact_phone')->nullable()->after('emergency_contact_name');
            $table->string('emergency_contact_relation')->nullable()->after('emergency_contact_phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'nik', 'gender', 'place_of_birth', 'date_of_birth', 'religion', 'marital_status',
                'last_education', 'join_date', 'employee_id', 'employment_status',
                'bank_name', 'bank_account_number', 'bank_account_holder', 'npwp', 'basic_salary',
                'emergency_contact_name', 'emergency_contact_phone', 'emergency_contact_relation'
            ]);
        });
    }
};
