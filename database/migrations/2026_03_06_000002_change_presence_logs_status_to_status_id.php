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
        Schema::table('presence_logs', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('presence_logs', function (Blueprint $table) {
            $table->foreignId('status_id')->nullable()->after('user_id')->constrained('statuses')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('presence_logs', function (Blueprint $table) {
            $table->dropForeign(['status_id']);
        });

        Schema::table('presence_logs', function (Blueprint $table) {
            $table->string('status')->after('user_id');
        });
    }
};
