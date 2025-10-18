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
        Schema::table('suppliers', function (Blueprint $table) {
            // Tedarikçi ülkesi ve şehri
            $table->string('country', 100)->nullable()->after('name');
            $table->string('city', 100)->nullable()->after('country');
            
            // Muhasebe kodu
            $table->string('accounting_code', 50)->nullable()->after('city');
            
            // Ödeme periyodları
            $table->json('payment_periods')->nullable()->after('accounting_code');
            
            // Ödeme tipi
            $table->enum('payment_type', ['cari', 'credit_card'])->default('cari')->after('payment_periods');
            
            // İletişim yetkilileri
            $table->json('contact_persons')->nullable()->after('payment_type');
            
            // Tedarikçi adresi
            $table->text('address')->nullable()->after('contact_persons');
            
            // Tedarikçi logosu
            $table->string('logo')->nullable()->after('address');
            
            // Vergi oranı
            $table->decimal('tax_rate', 5, 2)->default(0.00)->after('logo');
            
            // Tedarikçi epostaları
            $table->json('emails')->nullable()->after('tax_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn([
                'country',
                'city', 
                'accounting_code',
                'payment_periods',
                'payment_type',
                'contact_persons',
                'address',
                'logo',
                'tax_rate',
                'emails'
            ]);
        });
    }
};