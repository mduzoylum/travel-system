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
            // Eksik kolonları kontrol et ve ekle
            if (!Schema::hasColumn('suppliers', 'auto_sync_enabled')) {
                $table->boolean('auto_sync_enabled')->default(false)->after('sync_enabled')
                      ->comment('Otomatik senkronizasyon etkin mi');
            }
            if (!Schema::hasColumn('suppliers', 'notification_enabled')) {
                $table->boolean('notification_enabled')->default(true)->after('auto_sync_enabled')
                      ->comment('Bildirim etkin mi');
            }
            if (!Schema::hasColumn('suppliers', 'max_daily_bookings')) {
                $table->integer('max_daily_bookings')->nullable()->after('notification_enabled')
                      ->comment('Günlük maksimum rezervasyon sayısı');
            }
            if (!Schema::hasColumn('suppliers', 'priority_level')) {
                $table->integer('priority_level')->default(1)->after('max_daily_bookings')
                      ->comment('Öncelik seviyesi (1-5)');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn([
                'auto_sync_enabled',
                'notification_enabled', 
                'max_daily_bookings',
                'priority_level'
            ]);
        });
    }
};
