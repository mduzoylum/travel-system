<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed in order to maintain referential integrity
        
        // 1. Users (no dependencies)
        $this->call([
            UserSeeder::class,
        ]);

        // 2. Firms (no dependencies)
        $this->call([
            FirmSeeder::class,
        ]);

        // 3. Firm Users (depends on Users and Firms)
        $this->call([
            FirmUserSeeder::class,
        ]);

        // 4. Suppliers (no dependencies)
        $this->call([
            SupplierSeeder::class,
        ]);

        // 5. Hotels (depends on Suppliers)
        $this->call([
            HotelSeeder::class,
        ]);

        // 6. Contracts (depends on Hotels and Firms)
        $this->call([
            ContractSeeder::class,
        ]);

        // 7. Contract Rooms (depends on Contracts)
        $this->call([
            ContractRoomSeeder::class,
        ]);

        // 8. Room Availability (depends on Contract Rooms)
        $this->call([
            RoomAvailabilitySeeder::class,
        ]);

        // 9. Credit Accounts (depends on Firms)
        $this->call([
            CreditAccountSeeder::class,
        ]);

        // 10. Credit Transactions (depends on Credit Accounts)
        $this->call([
            CreditTransactionSeeder::class,
        ]);

        // 11. User Access Rules (depends on Firms)
        $this->call([
            UserAccessRuleSeeder::class,
        ]);

        // 12. Approval Scenarios (depends on Firms)
        $this->call([
            ApprovalScenarioSeeder::class,
        ]);

        // 13. Approval Rules (depends on Approval Scenarios)
        $this->call([
            ApprovalRuleSeeder::class,
        ]);

        // 14. Approval Approvers (depends on Approval Scenarios and Users)
        $this->call([
            ApprovalApproverSeeder::class,
        ]);

        // 15. Reservations (depends on Contract Rooms and Users)
        $this->call([
            ReservationSeeder::class,
        ]);

        // 16. Approval Requests (depends on Reservations and Approval Scenarios)
        $this->call([
            ApprovalRequestSeeder::class,
        ]);

        // 17. Service Fees (depends on Firms)
        $this->call([
            ServiceFeeSeeder::class,
        ]);

        // 18. Profit Rules (depends on Firms and Suppliers)
        $this->call([
            ProfitRuleSeeder::class,
        ]);

        // 19. Profit Calculations (depends on Reservations, Contracts, Firms, Suppliers)
        $this->call([
            ProfitCalculationSeeder::class,
        ]);
    }
}
