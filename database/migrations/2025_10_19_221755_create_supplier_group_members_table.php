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
        Schema::create('supplier_group_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
            $table->foreignId('supplier_group_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            // Aynı tedarikçi aynı gruba birden fazla kez eklenmesin
            $table->unique(['supplier_id', 'supplier_group_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_group_members');
    }
};
