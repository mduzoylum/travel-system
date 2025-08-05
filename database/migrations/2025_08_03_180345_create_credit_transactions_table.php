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
        Schema::create('credit_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('credit_account_id')->constrained('credit_accounts')->onDelete('cascade');
            $table->enum('type', ['credit', 'debit']);
            $table->decimal('amount', 15, 2);
            $table->text('description')->nullable();
            $table->string('reference_type')->nullable(); // reservation, manual, payment
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->decimal('balance_after', 15, 2);
            $table->timestamps();
            
            $table->index(['credit_account_id', 'type']);
            $table->index(['reference_type', 'reference_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_transactions');
    }
};
