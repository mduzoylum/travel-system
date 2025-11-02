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
        Schema::create('supplier_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hotels')->onDelete('cascade');
            $table->foreignId('reservation_id')->nullable()->constrained('reservations')->onDelete('set null');
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('TRY');
            $table->string('payment_type'); // 'cari' veya 'credit_card'
            $table->date('due_date'); // Ödeme tarihi
            $table->enum('status', ['pending', 'paid', 'cancelled'])->default('pending');
            $table->date('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['hotel_id', 'status', 'due_date']);
            $table->index(['status', 'due_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_payments');
    }
};
