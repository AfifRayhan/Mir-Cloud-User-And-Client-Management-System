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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('status_id')->nullable()->constrained('customer_statuses');
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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
