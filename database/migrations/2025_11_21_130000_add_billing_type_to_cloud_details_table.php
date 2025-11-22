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
        Schema::table('cloud_details', function (Blueprint $table) {
            $table->enum('billing_type', ['billable', 'test'])->default('billable')->after('internet');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cloud_details', function (Blueprint $table) {
            $table->dropColumn('billing_type');
        });
    }
};

