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
        Schema::create('approval_scenarios', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('firm_id');
            $table->boolean('is_active')->default(true);
            $table->enum('approval_type', ['single', 'multi_step', 'parallel']);
            $table->integer('max_approval_days')->default(7);
            $table->boolean('require_all_approvers')->default(false);
            $table->json('notification_settings')->nullable();
            $table->timestamps();
            
            $table->foreign('firm_id')->references('id')->on('firms')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_scenarios');
    }
};
