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
        // --- BASE LARAVEL TABLES ---

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });

        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });

        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->integer('total_jobs');
            $table->integer('pending_jobs');
            $table->integer('failed_jobs');
            $table->longText('failed_job_ids');
            $table->mediumText('options')->nullable();
            $table->integer('cancelled_at')->nullable();
            $table->integer('created_at');
            $table->integer('finished_at')->nullable();
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });

        // --- APPLICATION TABLES ---

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
            $table->foreignId('platform_id')->nullable()->constrained('platforms')->cascadeOnDelete();
            $table->string('service_name');
            $table->string('unit')->nullable();
            $table->decimal('unit_price', 10, 2)->nullable();
            $table->foreignId('inserted_by')->nullable()->constrained('users');
            $table->timestamps();

            $table->unique(['service_name', 'platform_id']);
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
            $table->date('customer_activation_date');
            $table->text('customer_address')->nullable();
            $table->string('bin_number')->nullable();
            $table->string('po_number')->nullable();
            $table->json('po_project_sheets')->nullable();

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
            $table->dateTime('assignment_datetime')->nullable();
            $table->dateTime('deadline_datetime')->nullable();
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
            $table->dateTime('assignment_datetime')->nullable();
            $table->dateTime('deadline_datetime')->nullable();
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

        // 15. VDCs
        Schema::create('vdcs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->string('vdc_name');
            $table->timestamps();

            // Unique constraint: one VDC name per customer
            $table->unique(['customer_id', 'vdc_name']);
        });

        // 14. Tasks
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('task_id')->unique()->nullable();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('status_id')->nullable()->constrained('customer_statuses');
            $table->foreignId('task_status_id')->default(1)->constrained('task_statuses');
            $table->foreignId('vdc_id')->nullable()->constrained('vdcs')->nullOnDelete();
            $table->date('activation_date');
            $table->timestamp('assignment_datetime')->nullable();
            $table->timestamp('deadline_datetime')->nullable();
            $table->enum('allocation_type', ['upgrade', 'downgrade']);
            $table->boolean('has_resource_conflict')->default(false);

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

        // 16. Summaries
        Schema::create('summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
            $table->integer('test_quantity')->default(0);
            $table->integer('billable_quantity')->default(0);
            $table->timestamps();

            // Unique constraint: one summary record per customer-service pair
            $table->unique(['customer_id', 'service_id']);
        });

        // 17. Resource Transfers
        Schema::create('resource_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('status_from_id')->constrained('customer_statuses');
            $table->foreignId('status_to_id')->constrained('customer_statuses');
            $table->dateTime('transfer_datetime');
            $table->foreignId('inserted_by')->constrained('users');
            $table->timestamps();
        });

        // 18. Resource Transfer Details
        Schema::create('resource_transfer_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resource_transfer_id')->constrained('resource_transfers')->onDelete('cascade');
            $table->foreignId('service_id')->constrained('services');
            $table->integer('current_source_quantity');
            $table->integer('current_target_quantity');
            $table->integer('transfer_amount');
            $table->integer('new_source_quantity');
            $table->integer('new_target_quantity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resource_transfer_details');
        Schema::dropIfExists('resource_transfers');
        Schema::dropIfExists('summaries');
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('vdcs');
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
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
