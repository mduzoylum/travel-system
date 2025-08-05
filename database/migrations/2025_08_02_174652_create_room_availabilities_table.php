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
        Schema::create('room_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_room_id')->constrained()->onDelete('cascade');
            $table->date('date');           // Hangi gün için geçerli
            $table->integer('stock');       // Maksimum satış yapılabilecek sayı
            $table->timestamps();

            $table->unique(['contract_room_id', 'date']); // Aynı oda aynı günde 1 kayıt olmalı
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_availabilities');
    }
};
