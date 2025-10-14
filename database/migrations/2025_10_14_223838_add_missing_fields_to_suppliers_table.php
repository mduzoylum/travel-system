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
        Schema::table('suppliers', function (Blueprint $table) {
            $table->text('description')->nullable()->after('type');
            $table->string('api_version', 20)->nullable()->after('api_endpoint');
            $table->string('sync_frequency', 50)->nullable()->after('api_credentials');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn(['description', 'api_version', 'sync_frequency']);
        });
    }
};
