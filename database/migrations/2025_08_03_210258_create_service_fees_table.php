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
        Schema::create('service_fees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('firm_id')->nullable();
            $table->enum('service_type', ['reservation', 'cancellation', 'modification', 'booking'])->default('reservation');
            $table->enum('fee_type', ['percentage', 'fixed'])->default('fixed');
            $table->decimal('fee_value', 10, 2);
            $table->decimal('min_amount', 10, 2)->nullable();
            $table->decimal('max_amount', 10, 2)->nullable();
            $table->string('currency', 3)->default('TRY');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_mandatory')->default(false);
            $table->timestamps();
            
            $table->foreign('firm_id')->references('id')->on('firms')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_fees');
    }
};
