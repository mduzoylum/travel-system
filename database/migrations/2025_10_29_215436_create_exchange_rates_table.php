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
        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->string('from_currency', 3);                      // Kaynak para birimi
            $table->string('to_currency', 3);                        // Hedef para birimi
            $table->decimal('rate', 10, 4);                          // Döviz kuru (ör: EUR -> TRY için 35.50)
            $table->date('valid_from')->nullable();                  // Geçerlilik başlangıç tarihi
            $table->date('valid_until')->nullable();                 // Geçerlilik bitiş tarihi
            $table->boolean('is_active')->default(true);             // Aktif mi?
            $table->timestamps();

            // Benzersizlik kontrolü
            $table->unique(['from_currency', 'to_currency', 'valid_from'], 'unique_rate');
            $table->index(['from_currency', 'to_currency']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exchange_rates');
    }
};
