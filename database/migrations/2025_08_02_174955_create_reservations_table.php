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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('contract_room_id')->constrained()->onDelete('cascade');
            $table->date('checkin_date');
            $table->date('checkout_date');
            $table->integer('guest_count')->default(1);
            $table->decimal('total_price', 10, 2);
            $table->enum('status', ['pending', 'awaiting_approval', 'approved', 'rejected', 'cancelled'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
