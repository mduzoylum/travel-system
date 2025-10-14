<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Önce mevcut type değerlerini backup al
        $suppliers = DB::table('suppliers')->get();
        
        Schema::table('suppliers', function (Blueprint $table) {
            // Mevcut type kolonunu sil
            $table->dropColumn('type');
        });
        
        Schema::table('suppliers', function (Blueprint $table) {
            // Yeni JSON type kolonu ekle
            $table->json('types')->nullable()->after('name');
        });
        
        // Mevcut verileri yeni formata çevir
        foreach ($suppliers as $supplier) {
            if ($supplier->type) {
                DB::table('suppliers')
                    ->where('id', $supplier->id)
                    ->update(['types' => json_encode([$supplier->type])]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // JSON types'dan ilk değeri al ve type kolonuna çevir
        $suppliers = DB::table('suppliers')->get();
        
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn('types');
        });
        
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('type', 50)->nullable()->after('name');
        });
        
        // JSON verilerini tek type'a çevir
        foreach ($suppliers as $supplier) {
            if ($supplier->types) {
                $types = json_decode($supplier->types, true);
                $firstType = is_array($types) && !empty($types) ? $types[0] : null;
                DB::table('suppliers')
                    ->where('id', $supplier->id)
                    ->update(['type' => $firstType]);
            }
        }
    }
};
