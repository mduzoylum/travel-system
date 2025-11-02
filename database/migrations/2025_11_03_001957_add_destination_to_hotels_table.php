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
        Schema::table('hotels', function (Blueprint $table) {
            $table->foreignId('country_id')->nullable()->after('country')->constrained('destinations')->onDelete('set null');
            $table->foreignId('city_id')->nullable()->after('city')->constrained('destinations')->onDelete('set null');
            $table->foreignId('sub_destination_id')->nullable()->after('city_id')->constrained('destinations')->onDelete('set null');
            
            $table->index(['country_id', 'city_id', 'sub_destination_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->dropForeign(['country_id']);
            $table->dropForeign(['city_id']);
            $table->dropForeign(['sub_destination_id']);
            $table->dropColumn(['country_id', 'city_id', 'sub_destination_id']);
        });
    }
};
