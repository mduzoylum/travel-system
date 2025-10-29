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
        Schema::create('contract_room_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_room_id')->constrained()->onDelete('cascade');
            $table->date('start_date');                              // Periyot başlangıç tarihi
            $table->date('end_date');                                // Periyot bitiş tarihi
            $table->string('currency', 3)->default('TRY');          // Para birimi (TRY, EUR, USD, vb.)
            $table->decimal('base_price', 10, 2);                     // Otelden alınan maliyet
            $table->decimal('sale_price', 10, 2);                    // Müşteriye satılan fiyat
            $table->text('notes')->nullable();                       // Notlar
            $table->boolean('is_active')->default(true);             // Aktif mi?
            $table->timestamps();

            // Aynı tarih aralığında çakışma olmaması için index
            $table->index(['contract_room_id', 'start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_room_periods');
    }
};
