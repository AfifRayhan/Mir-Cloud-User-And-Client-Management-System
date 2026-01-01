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
        Schema::table('services', function (Blueprint $table) {
            $table->foreignId('platform_id')->nullable()->after('id')->constrained('platforms')->cascadeOnDelete();
            $table->dropUnique('services_service_name_unique');
            $table->unique(['service_name', 'platform_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropUnique(['service_name', 'platform_id']);
            $table->unique('service_name');
            $table->dropForeign(['platform_id']);
            $table->dropColumn('platform_id');
        });
    }
};
