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
        // Mevcut kayıtlara benzersiz ID atamalarını yap
        $hotels = \DB::table('hotels')->whereNull('unique_id')->orWhere('unique_id', '')->get();
        
        foreach ($hotels as $hotel) {
            do {
                $length = rand(8, 9);
                $uniqueId = str_pad((string) rand(0, 999999999), $length, '0', STR_PAD_LEFT);
            } while (\DB::table('hotels')->where('unique_id', $uniqueId)->exists());
            
            \DB::table('hotels')->where('id', $hotel->id)->update(['unique_id' => $uniqueId]);
        }
        
        // Şimdi unique constraint ekle
        Schema::table('hotels', function (Blueprint $table) {
            $table->string('unique_id')->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
