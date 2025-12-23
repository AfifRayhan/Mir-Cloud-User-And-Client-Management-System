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
        // 1. Roles
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('role_name')->unique();
            $table->timestamps();
        });

        // 2. Platforms
        Schema::create('platforms', function (Blueprint $table) {
            $table->id();
            $table->string('platform_name')->unique();
            $table->timestamps();
        });

        // 3. User Departments
        Schema::create('user_departments', function (Blueprint $table) {
            $table->id();
            $table->string('department_name')->unique();
            $table->timestamps();
        });

        // 4. Task Statuses
        Schema::create('task_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->timestamps();
        });

        // 5. Customer Statuses
        Schema::create('customer_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        // 6. Services
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('service_name')->unique();
            $table->string('unit')->nullable();
            $table->decimal('unit_price', 10, 2)->nullable();
            $table->foreignId('inserted_by')->nullable()->constrained('users');
            $table->timestamps();
        });

        // 7. Update Users Table
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->nullable()->after('email');
            $table->string('phone')->nullable()->after('username');
            $table->string('designation')->nullable()->after('phone');
            $table->foreignId('role_id')->nullable()->after('password')->constrained('roles')->nullOnDelete();
            $table->foreignId('department_id')->nullable()->after('role_id')->constrained('user_departments')->nullOnDelete();
            $table->foreignId('user_created_by')->nullable()->after('department_id')->constrained('users')->nullOnDelete();
            $table->timestamp('first_login_at')->nullable()->after('user_created_by');
        });

        // 8. Customers
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->date('activation_date');
            $table->text('customer_address')->nullable();
            $table->string('bin_number')->nullable();
            $table->string('po_number')->nullable();

            // Contacts
            $table->string('commercial_contact_name')->nullable();
            $table->string('commercial_contact_designation')->nullable();
            $table->string('commercial_contact_email')->nullable();
            $table->string('commercial_contact_phone')->nullable();

            $table->string('technical_contact_name')->nullable();
            $table->string('technical_contact_designation')->nullable();
            $table->string('technical_contact_email')->nullable();
            $table->string('technical_contact_phone')->nullable();

            $table->string('optional_contact_name')->nullable();
            $table->string('optional_contact_designation')->nullable();
            $table->string('optional_contact_email')->nullable();
            $table->string('optional_contact_phone')->nullable();

            $table->foreignId('platform_id')->nullable()->constrained('platforms')->nullOnDelete();
            $table->foreignId('submitted_by')->nullable()->constrained('users');
            $table->foreignId('processed_by')->nullable()->constrained('users');

            $table->timestamps();
        });

        // 10. Resource Upgradations
        Schema::create('resource_upgradations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('status_id')->nullable()->constrained('customer_statuses');
            $table->date('activation_date');
            $table->date('inactivation_date')->default('3000-01-01');
            $table->foreignId('task_status_id')->constrained('task_statuses');
            $table->foreignId('inserted_by')->constrained('users');
            $table->timestamps();
        });

        // 11. Resource Upgradation Details
        Schema::create('resource_upgradation_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resource_upgradation_id')->constrained('resource_upgradations')->onDelete('cascade');
            $table->foreignId('service_id')->constrained('services');
            $table->integer('quantity');
            $table->integer('upgrade_amount')->nullable();
            $table->timestamps();
        });

        // 12. Resource Downgradations
        Schema::create('resource_downgradations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('status_id')->nullable()->constrained('customer_statuses');
            $table->date('activation_date');
            $table->date('inactivation_date')->default('3000-01-01');
            $table->foreignId('task_status_id')->constrained('task_statuses');
            $table->foreignId('inserted_by')->constrained('users');
            $table->timestamps();
        });

        // 13. Resource Downgradation Details
        Schema::create('resource_downgradation_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resource_downgradation_id')->constrained('resource_downgradations')->onDelete('cascade');
            $table->foreignId('service_id')->constrained('services');
            $table->integer('quantity');
            $table->integer('downgrade_amount')->nullable();
            $table->timestamps();
        });

        // 14. Tasks
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('status_id')->nullable()->constrained('customer_statuses');
            $table->foreignId('task_status_id')->default(1)->constrained('task_statuses');
            $table->date('activation_date');
            $table->enum('allocation_type', ['upgrade', 'downgrade']);

            // Reference to either upgrade or downgrade (only one will be set)
            $table->foreignId('resource_upgradation_id')->nullable()->constrained('resource_upgradations')->onDelete('cascade');
            $table->foreignId('resource_downgradation_id')->nullable()->constrained('resource_downgradations')->onDelete('cascade');

            // Assignment tracking
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();
        });

        // 15. VDCs
        Schema::create('vdcs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->string('vdc_name');
            $table->timestamps();

            // Unique constraint: one VDC name per customer
            $table->unique(['customer_id', 'vdc_name']);
        });

        // 16. Summaries
        Schema::create('summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
            $table->integer('quantity')->default(0);
            $table->timestamps();

            // Unique constraint: one summary record per customer-service pair
            $table->unique(['customer_id', 'service_id']);
        });

        // 17. Add VDC to Tasks
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignId('vdc_id')->nullable()->after('task_status_id')->constrained('vdcs')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['vdc_id']);
            $table->dropColumn('vdc_id');
        });

        Schema::dropIfExists('summaries');
        Schema::dropIfExists('vdcs');
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('resource_downgradation_details');
        Schema::dropIfExists('resource_downgradations');
        Schema::dropIfExists('resource_upgradation_details');
        Schema::dropIfExists('resource_upgradations');
        Schema::dropIfExists('customers');

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropForeign(['department_id']);
            $table->dropForeign(['user_created_by']);
            $table->dropColumn(['username', 'phone', 'designation', 'role_id', 'department_id', 'user_created_by', 'first_login_at']);
        });

        Schema::dropIfExists('services');
        Schema::dropIfExists('customer_statuses');
        Schema::dropIfExists('task_statuses');
        Schema::dropIfExists('user_departments');
        Schema::dropIfExists('platforms');
        Schema::dropIfExists('roles');
    }
};
