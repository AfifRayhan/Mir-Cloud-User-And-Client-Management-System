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
        Schema::table('summaries', function (Blueprint $table) {
            $table->dropForeign(['vdc_id']);
            $table->dropColumn('vdc_id');
        });

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

        Schema::table('summaries', function (Blueprint $table) {
            $table->foreignId('vdc_id')->nullable()->constrained('vdcs')->nullOnDelete();
        });
    }
};
