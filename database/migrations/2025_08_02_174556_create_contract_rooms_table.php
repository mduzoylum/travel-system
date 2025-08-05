<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('contract_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained()->onDelete('cascade');
            $table->string('room_type');          // örn: Deluxe, Standart
            $table->string('meal_plan');          // örn: BB, HB, FB, ALL
            $table->decimal('base_price', 10, 2); // otelden alınan fiyat
            $table->decimal('sale_price', 10, 2); // müşteriye satılan fiyat
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_rooms');
    }
};
