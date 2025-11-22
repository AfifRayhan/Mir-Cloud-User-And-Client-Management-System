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
        Schema::table('customers', function (Blueprint $table) {
            if (Schema::hasColumn('customers', 'platform_id')) {
                $table->dropColumn('platform_id');
            }
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->foreignId('platform_id')
                ->nullable()
                ->after('optional_contact_phone')
                ->constrained('platforms')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['platform_id']);
            $table->dropColumn('platform_id');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->unsignedBigInteger('platform_id')->nullable()->after('optional_contact_phone');
        });
    }
};

