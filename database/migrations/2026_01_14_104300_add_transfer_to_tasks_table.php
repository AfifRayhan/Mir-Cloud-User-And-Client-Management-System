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
        Schema::table('tasks', function (Blueprint $table) {
            $table->enum('allocation_type', ['upgrade', 'downgrade', 'transfer'])->change();
            $table->foreignId('resource_transfer_id')->nullable()->after('resource_downgradation_id')->constrained('resource_transfers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['resource_transfer_id']);
            $table->dropColumn('resource_transfer_id');
            $table->enum('allocation_type', ['upgrade', 'downgrade'])->change();
        });
    }
};
