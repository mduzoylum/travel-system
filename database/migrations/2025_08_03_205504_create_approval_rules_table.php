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
        Schema::create('approval_rules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('scenario_id');
            $table->enum('rule_type', ['price_range', 'destination', 'duration', 'amount', 'custom']);
            $table->string('field_name');
            $table->enum('operator', ['equals', 'not_equals', 'greater_than', 'less_than', 'between', 'in', 'not_in']);
            $table->json('value');
            $table->integer('priority')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->foreign('scenario_id')->references('id')->on('approval_scenarios')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_rules');
    }
};
