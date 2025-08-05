<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('hotels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('city');
            $table->string('country');
            $table->unsignedTinyInteger('stars'); // 1-5 arası
            $table->decimal('min_price', 10, 2); // en ucuz oda fiyatı (erişim kurallarında kullanılabilir)
            $table->boolean('is_contracted')->default(true); // sözleşmeli mi
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotels');
    }
};
