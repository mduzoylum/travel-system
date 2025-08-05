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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // otelbest, x_firm, y_firm, etc.
            $table->string('api_endpoint')->nullable();
            $table->json('api_credentials')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('sync_enabled')->default(false);
            $table->timestamp('last_sync_at')->nullable();
            $table->timestamps();
            
            $table->index(['type', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
