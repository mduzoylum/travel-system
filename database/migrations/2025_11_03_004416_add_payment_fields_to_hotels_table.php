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
            $table->enum('payment_type', ['cari', 'credit_card'])->nullable()->after('supplier_id');
            $table->string('payment_period_type')->nullable()->after('payment_type'); // 'days_before_checkin', 'days_after_checkin', 'specific_day_of_month'
            $table->integer('payment_period_value')->nullable()->after('payment_period_type'); // gün sayısı veya ayın günü
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->dropColumn(['payment_type', 'payment_period_type', 'payment_period_value']);
        });
    }
};
