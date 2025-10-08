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
        Schema::table('contracts', function (Blueprint $table) {
            $table->decimal('base_price', 10, 2)->nullable()->after('currency');
            $table->decimal('commission_rate', 5, 2)->default(0)->after('base_price');
            $table->decimal('service_fee', 10, 2)->nullable()->after('commission_rate');
            $table->text('description')->nullable()->after('service_fee');
            $table->boolean('auto_renewal')->default(false)->after('description');
            $table->string('payment_terms', 100)->nullable()->after('auto_renewal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn([
                'base_price',
                'commission_rate', 
                'service_fee',
                'description',
                'auto_renewal',
                'payment_terms'
            ]);
        });
    }
};