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
        Schema::table('service_fees', function (Blueprint $table) {
            $table->enum('product_type', ['hotel', 'flight', 'car', 'activity', 'transfer', 'all'])
                  ->default('all')
                  ->after('service_type')
                  ->comment('Ürün tipi: hotel, flight, car, activity, transfer, all (tümü)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_fees', function (Blueprint $table) {
            $table->dropColumn('product_type');
        });
    }
};