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
        // Hotels tablosuna accounting_code ekle
        Schema::table('hotels', function (Blueprint $table) {
            $table->string('accounting_code', 50)->nullable()->after('supplier_id');
        });
        
        // Reservations tablosuna accounting_code ekle
        Schema::table('reservations', function (Blueprint $table) {
            $table->string('accounting_code', 50)->nullable()->after('total_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->dropColumn('accounting_code');
        });
        
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn('accounting_code');
        });
    }
};
