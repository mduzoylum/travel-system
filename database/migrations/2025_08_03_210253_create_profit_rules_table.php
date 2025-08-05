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
        Schema::create('profit_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('firm_id')->nullable();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->string('destination')->nullable();
            $table->enum('trip_type', ['domestic', 'international'])->default('domestic');
            $table->enum('travel_type', ['one_way', 'round_trip'])->default('round_trip');
            $table->enum('fee_type', ['percentage', 'fixed', 'tiered'])->default('percentage');
            $table->decimal('fee_value', 10, 2);
            $table->decimal('min_fee', 10, 2)->nullable();
            $table->decimal('max_fee', 10, 2)->nullable();
            $table->json('tier_rules')->nullable(); // Katmanlı fiyatlandırma için
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(0);
            $table->timestamps();
            
            $table->foreign('firm_id')->references('id')->on('firms')->onDelete('cascade');
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profit_rules');
    }
};
