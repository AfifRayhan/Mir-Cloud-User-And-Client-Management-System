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
        Schema::create('cloud_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->smallInteger('vcpu')->nullable();
            $table->smallInteger('ram')->nullable();
            $table->smallInteger('storage')->nullable();
            $table->boolean('real_ip')->default(false);
            $table->boolean('vpn')->default(false);
            $table->boolean('bdix')->default(false);
            $table->smallInteger('internet')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cloud_details');
    }
};
