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
        Schema::dropIfExists('cloud_details');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate table for rollback
        Schema::create('cloud_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->integer('vcpu')->nullable();
            $table->integer('ram')->nullable();
            $table->integer('storage')->nullable();
            $table->integer('real_ip')->nullable();
            $table->integer('vpn')->nullable();
            $table->integer('bdix')->nullable();
            $table->integer('internet')->nullable();
            $table->json('other_configuration')->nullable();
            $table->foreignId('inserted_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }
};
