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
        Schema::create('destinations', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Ülke, şehir veya alt destinasyon adı
            $table->enum('type', ['country', 'city', 'sub_destination']); // Türü
            $table->foreignId('parent_id')->nullable()->constrained('destinations')->onDelete('cascade'); // Hiyerarşi için
            $table->string('code', 10)->nullable(); // İsteğe bağlı kod (örn: TR, IST)
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0); // Sıralama
            $table->timestamps();
            
            $table->index(['type', 'parent_id']);
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('destinations');
    }
};
