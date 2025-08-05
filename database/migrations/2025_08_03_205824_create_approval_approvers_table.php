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
        Schema::create('approval_approvers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('scenario_id');
            $table->unsignedBigInteger('user_id');
            $table->integer('step_order')->default(1);
            $table->enum('approval_type', ['required', 'optional', 'backup']);
            $table->boolean('can_override')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->foreign('scenario_id')->references('id')->on('approval_scenarios')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['scenario_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_approvers');
    }
};
