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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->date('activation_date');
            $table->text('customer_address')->nullable();
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
            $table->unsignedBigInteger('platform_id')->nullable();
            $table->enum('status', ['pending', 'processing', 'completed', 'cancel'])->default('pending');
            $table->foreignId('submitted_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('processed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
