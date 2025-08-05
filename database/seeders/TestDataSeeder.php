<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // This seeder will populate the database with comprehensive test data
        // including all related entities with proper relationships
        
        $this->call([
            UserSeeder::class,
            FirmSeeder::class,
            FirmUserSeeder::class,
            SupplierSeeder::class,
            HotelSeeder::class,
            ContractSeeder::class,
            ContractRoomSeeder::class,
            RoomAvailabilitySeeder::class,
            CreditAccountSeeder::class,
            CreditTransactionSeeder::class,
            UserAccessRuleSeeder::class,
            ApprovalScenarioSeeder::class,
            ApprovalRuleSeeder::class,
            ApprovalApproverSeeder::class,
            ReservationSeeder::class,
            ApprovalRequestSeeder::class,
            ServiceFeeSeeder::class,
            ProfitRuleSeeder::class,
            ProfitCalculationSeeder::class,
        ]);

        $this->command->info('✅ Tüm test verileri başarıyla oluşturuldu!');
        $this->command->info('📊 Oluşturulan veriler:');
        $this->command->info('   - Kullanıcılar: 8 adet');
        $this->command->info('   - Firmalar: 3 adet');
        $this->command->info('   - Tedarikçiler: 4 adet');
        $this->command->info('   - Oteller: 9 adet');
        $this->command->info('   - Sözleşmeler: 12 adet');
        $this->command->info('   - Oda tipleri: 60+ adet');
        $this->command->info('   - Rezervasyonlar: 70 adet');
        $this->command->info('   - Onay istekleri: 20+ adet');
        $this->command->info('   - Kar hesaplamaları: 50+ adet');
        $this->command->info('');
        $this->command->info('🔑 Giriş bilgileri:');
        $this->command->info('   - Admin: admin@bizigo.com / password');
        $this->command->info('   - Kullanıcı: ahmet@bizigo.com / password');
    }
} 