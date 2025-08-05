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
        Schema::create('profit_calculations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reservation_id')->nullable();
            $table->unsignedBigInteger('contract_id')->nullable();
            $table->unsignedBigInteger('firm_id');
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->decimal('base_price', 10, 2);
            $table->decimal('service_fee', 10, 2)->default(0);
            $table->decimal('commission', 10, 2)->default(0);
            $table->decimal('profit_margin', 10, 2)->default(0);
            $table->decimal('total_cost', 10, 2);
            $table->decimal('sale_price', 10, 2);
            $table->decimal('profit_amount', 10, 2);
            $table->decimal('profit_percentage', 5, 2);
            $table->string('currency', 3)->default('TRY');
            $table->json('calculation_details')->nullable();
            $table->enum('status', ['draft', 'confirmed', 'cancelled'])->default('draft');
            $table->timestamps();
            
            $table->foreign('reservation_id')->references('id')->on('reservations')->onDelete('cascade');
            $table->foreign('contract_id')->references('id')->on('contracts')->onDelete('cascade');
            $table->foreign('firm_id')->references('id')->on('firms')->onDelete('cascade');
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profit_calculations');
    }
};
