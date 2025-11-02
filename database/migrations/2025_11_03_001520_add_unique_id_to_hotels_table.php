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
        // Kolon zaten canlıda eklenmiş durumda
        // Sadece column ekleme işlemi yapılacak (ilerideki migration constraint'i halleder)
        if (!Schema::hasColumn('hotels', 'unique_id')) {
            Schema::table('hotels', function (Blueprint $table) {
                $table->string('unique_id', 10)->nullable()->after('id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->dropUnique(['unique_id']);
            $table->dropColumn('unique_id');
        });
    }
};
